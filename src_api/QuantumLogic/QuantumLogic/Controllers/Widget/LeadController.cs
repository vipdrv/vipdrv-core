using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Leads;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.Core.Extensions.DateTimeEx;
using QuantumLogic.Core.Utils.ContentManager;
using QuantumLogic.Core.Utils.Export.Entity.Concrete.Excel;
using QuantumLogic.Core.Utils.Export.Entity.Concrete.Excel.DataModels;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Leads;
using QuantumLogic.WebApi.DataModels.Responses;
using QuantumLogic.WebApi.Providers.Export.Excel.Leads;
using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Dynamic.Core;
using System.Linq.Expressions;
using System.Threading.Tasks;
using QuantumLogic.Core.Domain.Services.Widget.Beverages;
using QuantumLogic.Core.Domain.Services.Widget.Experts;
using QuantumLogic.Core.Domain.Services.Widget.Routes;
using QuantumLogic.Core.Domain.Services.Widget.Sites;
using QuantumLogic.Core.Utils.Email;
using QuantumLogic.Core.Utils.Email.Providers.SendGrid;
using QuantumLogic.Core.Utils.Email.Services;
using QuantumLogic.Core.Utils.Email.Templates.TestDrive;
using QuantumLogic.Data.EFContext;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Booking;
using SendGrid.Helpers.Mail;

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

        [HttpPost("{siteId}/complete-booking")]
        public async Task<LeadFullDto> CompleteBooking(int siteId, [FromBody]CompleteBookingRequest request)
        {
            // TODO: validate request

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
                DateTime.Now);

            LeadFullDto result = await InnerCreateAsync(leadFullDto);

            var expert = await _expertDomainService.RetrieveAsync((int)request.ExpertId);
            var beverage = await _beverageDomainService.RetrieveAsync((int)request.BeverageId);
            var road = await _routeDomainService.RetrieveAsync((int)request.RoadId);
            var site = await _siteDomainService.RetrieveAsync(siteId);

            _testDriveEmailService.SendCompleteBookingEmail(
                new EmailAddress(request.BookingUser.Email, $"{request.BookingUser.FirstName} {request.BookingUser.LastName}"),
                new CompleteBookingEmailTemplate(
                    request.BookingUser.FirstName,
                    request.BookingUser.LastName,
                    request.BookingDateTime.Date + request.BookingDateTime.Time, // TODO: refactor
                    request.BookingCar.ImageUrl,
                    request.BookingCar.Title,
                    expert.Name,
                    beverage.Name,
                    road.Name));

            var emails = site.Contacts.Split(';')[0].Split(',');
            var newLeadNotificationEmailTemplate = new NewLeadNotificationEmailTemplate(
                request.BookingCar.Title,
                request.BookingCar.ImageUrl,
                request.BookingCar.VIN,
                request.BookingUser.FirstName,
                request.BookingUser.LastName,
                request.BookingUser.Phone,
                request.BookingUser.Email,
                request.BookingDateTime.Date + request.BookingDateTime.Time, // TODO: refactor
                expert.Name,
                beverage.Name,
                road.Name);

            foreach (var email in emails)
            {
                _testDriveEmailService.SendNewLeadNotificationEmail(new EmailAddress(email), newLeadNotificationEmailTemplate);
            }
            
            return result;
        }

        #endregion

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

        protected virtual Expression<Func<Lead, bool>> BuildRetrieveManyFilter(LeadGetAllRequest request)
        {
            return (entity) =>
                request.UserId == null || request.UserId == entity.Site.UserId &&
                //(String.IsNullOrEmpty(request.RecievedDateTime) || entity.RecievedUtc) &&
                (String.IsNullOrEmpty(request.FirstName) || !String.IsNullOrEmpty(entity.FirstName) && entity.FirstName.Contains(request.FirstName)) &&
                (String.IsNullOrEmpty(request.SecondName) || !String.IsNullOrEmpty(entity.SecondName) && entity.SecondName.Contains(request.SecondName)) &&
                (String.IsNullOrEmpty(request.Site) || !String.IsNullOrEmpty(entity.Site.Name) && entity.Site.Name.Contains(request.Site)) &&
                (String.IsNullOrEmpty(request.Email) || !String.IsNullOrEmpty(entity.UserEmail) && entity.UserEmail.Contains(request.Email)) &&
                (String.IsNullOrEmpty(request.Phone) || !String.IsNullOrEmpty(entity.UserPhone) && entity.UserPhone.Contains(request.Phone)) &&
                (String.IsNullOrEmpty(request.Expert) || !String.IsNullOrEmpty(entity.Expert.Name) && entity.Expert.Name.Contains(request.Expert)) &&
                (String.IsNullOrEmpty(request.Route) || !String.IsNullOrEmpty(entity.Route.Name) && entity.Route.Name.Contains(request.Route)) &&
                (String.IsNullOrEmpty(request.Beverage) || !String.IsNullOrEmpty(entity.Beverage.Name) && entity.Beverage.Name.Contains(request.Beverage));
        }
    }
}
