using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Leads;
using QuantumLogic.Core.Domain.Services.Widget.Sites;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.Core.Extensions.DateTimeEx;
using QuantumLogic.Core.Utils.Email;
using QuantumLogic.Core.Utils.Email.Data.Templates;
using QuantumLogic.Core.Utils.Export.Entity.Concrete.Excel;
using QuantumLogic.Core.Utils.Export.Entity.Concrete.Excel.DataModels;
using QuantumLogic.Core.Utils.Sms;
using QuantumLogic.Core.Utils.Sms.Templates;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads;
using QuantumLogic.WebApi.DataModels.Requests;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Booking;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Leads;
using QuantumLogic.WebApi.DataModels.Responses;
using QuantumLogic.WebApi.Providers.Export.Excel.Leads;
using SendGrid.Helpers.Mail;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;
using QuantumLogic.Core.Utils.Storage;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/lead")]
    public class LeadController : EntityController<Lead, int, LeadDto, LeadFullDto>
    {
        #region Injected dependencies

        protected readonly IContentManager ContentManager;
        protected readonly ISiteDomainService SiteDomainService;
        protected readonly ITestDriveEmailService TestDriveEmailService;
        protected readonly ISmsService SmsService;
        protected readonly IQLSession Session;

        #endregion

        #region Ctors

        public LeadController(
            IQLUnitOfWorkManager uowManager,
            ILeadDomainService domainService,
            IContentManager contentManager,
            ITestDriveEmailService testDriveEmailService,
            IQLSession session,
            ISiteDomainService siteDomainService)
            : base(uowManager, domainService)
        {
            TestDriveEmailService = testDriveEmailService;
            Session = session;
            SmsService = new TwilioSmsService();
            ContentManager = contentManager;
            SiteDomainService = siteDomainService;
        }

        #endregion

        #region CRUD

        [Authorize]
        [HttpGet("{id}")]
        public Task<LeadFullDto> GetAsync(int id)
        {
            return InnerGetAsync(id);
        }
        [HttpPost]
        public Task<LeadFullDto> CreateAsync([FromBody]LeadFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [Authorize]
        [HttpPut]
        public Task<LeadFullDto> UpdateAsync([FromBody]LeadFullDto request)
        {
            return InnerUpdateAsync(request);
        }
        [Authorize]
        [HttpDelete("{id}")]
        public Task DeleteAsync(int id)
        {
            return InnerDeleteAsync(id);
        }

        #endregion

        #region Methods to operate with many entities

        [Authorize]
        [HttpPost("get-all/{page?}/{pageSize?}")]
        public Task<GetAllResponse<LeadDto>> GetAllAsync([FromBody]LeadGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            return InnerGetAllAsync(BuildRetrieveManyFilter(request), request.Sorting, page, pageSize);
        }

        #endregion

        #region Custom methods

        [Authorize]
        [HttpPost("export/excel/{page?}/{pageSize?}")]
        public async Task<string> ExportDataToExcelAsync([FromBody]LeadGetAllRequest request, uint? page = null, uint? pageSize = null)
        {
            Uri fileUrl;
            TimeSpan timeZoneOffset = TimeSpan.Zero;
            string fileName = $"TestDrive-Leads-{DateTime.UtcNow.FormatUtcDateTimeToUserFriendlyString(timeZoneOffset, "yyyyMMddHHmmss")}";
            string worksheetsName = "leads";
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                ExcelExportSettings<Lead> settings = new ExcelExportSettings<Lead>(
                    fileName, worksheetsName,
                    DomainService, ContentManager,
                    ExcelExportLeadOptionsProvider.GetEntityOptions((r) => r.UseByDefault, (key) => key, timeZoneOffset),
                    BuildRetrieveManyFilter(request), request.Sorting, page ?? 0 * pageSize ?? 0, pageSize);
                fileUrl = await ExcelExportService<Lead>.ExportDataAsync(settings);
            }
            return fileUrl.ToString();
        }
        [Authorize]
        [HttpPatch("patch-is-new/{id}")]
        public async Task PatchIsNewAsync([FromBody]PatchBoolPropertyRequest request, int id)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((ILeadDomainService)DomainService).ChangeIsNewAsync(id, request.Value);
                await uow.CompleteAsync();
            }
        }
        [Authorize]
        [HttpPatch("patch-is-reached-by-manager/{id}")]
        public async Task PatchIsReachedByManagerAsync([FromBody]PatchBoolPropertyRequest request, int id)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((ILeadDomainService)DomainService).ChangeIsReachedByManagerAsync(id, request.Value);
                await uow.CompleteAsync();
            }
        }

        [HttpPost("{siteId}/complete-booking")]
        public async Task<LeadFullDto> CompleteBookingAsync(int siteId, [FromBody]CompleteBookingRequest request)
        {
            if (request == null)
            {
                throw new ArgumentNullException(nameof(request));
            }

            LeadFullDto leadFullDto = request.MapToLeadFullDto(siteId);
            leadFullDto.NormalizeAsRequest();
            Lead createdLead;
            using (var uow = UowManager.CurrentOrCreateNew())
            {
                Session.UserId = (await SiteDomainService.RetrieveAsync(siteId)).UserId;
                Lead stubEntity = await DomainService.CreateAsync(leadFullDto.MapToEntity());
                await uow.CompleteAsync();
                createdLead = await DomainService.RetrieveAsync(stubEntity.Id, false);
            }
            LeadFullDto createLeadFullDto = new LeadFullDto();
            createLeadFullDto.MapFromEntity(createdLead);

            #region Send EMAIL notifications

            Task<SendGrid.Response> sendCompleteBookingEmailTask = TestDriveEmailService
                .SendCompleteBookingEmail(
                    new EmailAddress(createdLead.UserEmail, $"{createdLead.FirstName} {createdLead.SecondName}"),
                    new CompleteBookingEmailTemplate(createdLead, request.TimeZoneOffset));

            Task<SendGrid.Response> sendNewLeadNotificationEmailTask = TestDriveEmailService
                .SendNewLeadNotificationEmail(
                    createdLead.Site.EmailAdresses.Select(r => new EmailAddress(r)).ToList(),
                    new NewLeadNotificationEmailTemplate(createdLead, request.TimeZoneOffset));

            Task<SendGrid.Response> sendAdfEmailTask = TestDriveEmailService
                .SendAdfEmail(
                    createdLead.Site.AdfEmailAdresses.Select(r => new EmailAddress(r)).ToList(),
                    new EleadAdfTemplate(createdLead, request.BookingVehicle, request.TimeZoneOffset));

            await Task.WhenAll(sendCompleteBookingEmailTask, sendNewLeadNotificationEmailTask, sendAdfEmailTask);
            // responses can be analyzed below via send...EmailTask.Result

            #endregion

            #region Send SMS notifications

            await SmsService.SendSms(createdLead.Site.PhoneNumbers, new NewLeadNotificationSmsTemplate(createdLead, request.TimeZoneOffset));

            #endregion

            return createLeadFullDto;
        }

        [HttpPost("{siteId}/send-sms")]
        public Task SendSmsAsync(int siteId, [FromBody]SmsNotificationRequest request)
        {
            if (request == null)
            {
                throw new ArgumentNullException(nameof(request));
            }

            return SmsService.SendSms(
                new List<string>()
                {
                    request.Phone
                }, 
                new CompleteBookingSmsTemplate(
                    request.VehicleTitle,
                    request.BookingDateTimeUtc,
                    request.TimeZoneOffset,
                    request.ExpertName,
                    request.BeverageName,
                    request.RoadName,
                    request.DealerName,
                    request.DealerPhone));
        }

        #endregion

        #region Helpers

        protected virtual Expression<Func<Lead, bool>> BuildRetrieveManyFilter(LeadGetAllRequest request)
        {
            return (entity) =>
                (!request.UserId.HasValue || request.UserId.Value == entity.Site.UserId) &&
                (!request.SiteId.HasValue || request.SiteId.Value == entity.SiteId) &&
                (!request.ExpertId.HasValue || request.ExpertId.Value == entity.ExpertId) &&
                (!request.RouteId.HasValue || request.RouteId.Value == entity.RouteId) &&
                (!request.BeverageId.HasValue || request.BeverageId.Value == entity.BeverageId) &&
                (!request.RecievedDateTimeUtc.HasValue || request.RecievedDateTimeUtc.Value <= entity.RecievedUtc) &&
                (!request.BookingDateTimeUtc.HasValue || request.BookingDateTimeUtc.Value <= entity.BookingDateTimeUtc) &&
                (!request.IsReachedByManager.HasValue || request.IsReachedByManager.Value == entity.IsReachedByManager) &&
                (String.IsNullOrEmpty(request.FullName) || !String.IsNullOrEmpty(entity.FullName) && entity.FullName.ToUpper().Contains(request.FullName.ToUpper())) &&
                (String.IsNullOrEmpty(request.FirstName) || !String.IsNullOrEmpty(entity.FirstName) && entity.FirstName.ToUpper().Contains(request.FirstName.ToUpper())) &&
                (String.IsNullOrEmpty(request.SecondName) || !String.IsNullOrEmpty(entity.SecondName) && entity.SecondName.ToUpper().Contains(request.SecondName.ToUpper())) &&
                (String.IsNullOrEmpty(request.Site) || !String.IsNullOrEmpty(entity.Site.Name) && entity.Site.Name.ToUpper().Contains(request.Site.ToUpper())) &&
                (String.IsNullOrEmpty(request.Email) || !String.IsNullOrEmpty(entity.UserEmail) && entity.UserEmail.ToUpper().Contains(request.Email.ToUpper())) &&
                (String.IsNullOrEmpty(request.Phone) || !String.IsNullOrEmpty(entity.UserPhone) && entity.UserPhone.Contains(request.Phone)) &&
                (String.IsNullOrEmpty(request.Expert) || !String.IsNullOrEmpty(entity.Expert.Name) && entity.Expert.Name.ToUpper().Contains(request.Expert.ToUpper())) &&
                (String.IsNullOrEmpty(request.Route) || !String.IsNullOrEmpty(entity.Route.Name) && entity.Route.Name.ToUpper().Contains(request.Route.ToUpper())) &&
                (String.IsNullOrEmpty(request.Beverage) || !String.IsNullOrEmpty(entity.Beverage.Name) && entity.Beverage.Name.ToUpper().Contains(request.Beverage.ToUpper()));
        }

        #endregion
    }
}
