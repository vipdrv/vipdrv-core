using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Beverages;
using QuantumLogic.Core.Domain.Services.Widget.Experts;
using QuantumLogic.Core.Domain.Services.Widget.Leads;
using QuantumLogic.Core.Domain.Services.Widget.Routes;
using QuantumLogic.Core.Domain.Services.Widget.Sites;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.Core.Extensions.DateTimeEx;
using QuantumLogic.Core.Utils.ContentManager;
using QuantumLogic.Core.Utils.Email.Services;
using QuantumLogic.Core.Utils.Email.Templates.TestDrive;
using QuantumLogic.Core.Utils.Export.Entity.Concrete.Excel;
using QuantumLogic.Core.Utils.Export.Entity.Concrete.Excel.DataModels;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads;
using QuantumLogic.WebApi.DataModels.Requests;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Booking;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Leads;
using QuantumLogic.WebApi.DataModels.Responses;
using QuantumLogic.WebApi.Providers.Export.Excel.Leads;
using SendGrid.Helpers.Mail;
using System;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;
using Microsoft.EntityFrameworkCore;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/lead")]
    public class LeadController : EntityController<Lead, int, LeadDto, LeadFullDto>
    {
        private readonly IExpertDomainService _expertDomainService;
        private readonly IBeverageDomainService _beverageDomainService;
        private readonly IRouteDomainService _routeDomainService;
        private readonly ITestDriveEmailService _testDriveEmailService;
        private readonly ISiteDomainService _siteDomainService;

        #region Injected dependencies

        public IContentManager ContentManager { get; private set; }

        #endregion

        #region Ctors

        public LeadController(
            IQLUnitOfWorkManager uowManager,
            ILeadDomainService domainService,
            IExpertDomainService expertDomainService,
            IBeverageDomainService beverageDomainService,
            IRouteDomainService routeDomainService,
            ISiteDomainService siteDomainService,
            IContentManager contentManager,
            ITestDriveEmailService testDriveEmailService)
            : base(uowManager, domainService)
        {
            _expertDomainService = expertDomainService;
            _beverageDomainService = beverageDomainService;
            _routeDomainService = routeDomainService;
            _siteDomainService = siteDomainService;
            _testDriveEmailService = testDriveEmailService;
            ContentManager = contentManager;
        }

        #endregion

        #region CRUD

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
            var leadFullDto = new LeadFullDto(
                0,
                siteId,
                (int)request.ExpertId,
                (int)request.BeverageId,
                (int)request.RoadId,
                request.BookingUser.FirstName,
                request.BookingUser.LastName,
                request.BookingUser.Phone,
                request.BookingUser.Email,
                request.BookingCar.ImageUrl,
                request.BookingCar.Title,
                request.BookingCar.Vin,
                $"{request.Calendar.Date} {request.Calendar.Time}");

            // LeadFullDto result = await InnerCreateAsync(leadFullDto);

            // TODO: rewrite this nightmare
            QuantumLogicDbContext context = new QuantumLogicDbContext();
            context.Leads.Add(leadFullDto.MapToEntity());

            context.SaveChanges();
            context.Dispose();

            var expert = await _expertDomainService.RetrieveAsync((int)request.ExpertId);
            var beverage = await _beverageDomainService.RetrieveAsync((int)request.BeverageId);
            var road = await _routeDomainService.RetrieveAsync((int)request.RoadId);
            var site = await _siteDomainService.RetrieveAsync(siteId);

            _testDriveEmailService.SendCompleteBookingEmail(
                new EmailAddress(request.BookingUser.Email, $"{request.BookingUser.FirstName} {request.BookingUser.LastName}"),
                new CompleteBookingEmailTemplate(
                    request.BookingUser.FirstName,
                    request.BookingUser.LastName,
                    request.Calendar.Date + " " + request.Calendar.Time,
                    request.BookingCar.ImageUrl,
                    request.BookingCar.Title,
                    expert.Name,
                    beverage.Name,
                    road.Name,
                    site.DealerName,
                    site.DealerAddress,
                    site.DealerPhone,
                    site.Url));

            var emails = site.NotificationContacts.Split(';')[0].Split(',');
            var newLeadNotificationEmailTemplate = new NewLeadNotificationEmailTemplate(
                request.BookingCar.Title,
                request.BookingCar.ImageUrl,
                request.BookingCar.Vin,
                request.BookingUser.FirstName,
                request.BookingUser.LastName,
                request.BookingUser.Phone,
                request.BookingUser.Email,
                request.Calendar.Date + " " + request.Calendar.Time,
                expert.Name,
                beverage.Name,
                road.Name);

            foreach (var email in emails)
            {
                _testDriveEmailService.SendNewLeadNotificationEmail(new EmailAddress(email), newLeadNotificationEmailTemplate);
            }

            return null;
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
