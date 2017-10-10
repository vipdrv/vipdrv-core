using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Leads;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.Core.Extensions.DateTimeEx;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Leads;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/lead")]
    public class LeadController : EntityController<Lead, int, LeadDto, LeadFullDto>
    {
        #region Ctors

        public LeadController(IQLUnitOfWorkManager uowManager, ILeadDomainService domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        #region CRUD

        [HttpGet("{id}")]
        public Task<LeadFullDto> Get(int id)
        {
            return InnerGetAsync(id);
        }
        [HttpPost]
        public Task<LeadFullDto> Create([FromBody]LeadFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [HttpPut]
        public Task<LeadFullDto> Update([FromBody]LeadFullDto request)
        {
            return InnerUpdateAsync(request);
        }
        [HttpDelete("{id}")]
        public Task Delete(int id)
        {
            return InnerDeleteAsync(id);
        }

        #endregion

        #region Custom methods

        [HttpPost("get-all/{page?}/{pageSize?}")]
        public Task<GetAllResponse<LeadDto>> GetAll([FromBody]LeadGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            return InnerGetAllAsync(BuildRetrieveManyFilter(request), request.Sorting, page, pageSize);
        }

        #endregion

        [HttpPost("export/excel/{page?}/{pageSize?}")]
        public async Task<string> ExportDataToExcelAsync([FromBody]LeadGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            string fileUrl;
            TimeSpan timeZoneOffset = TimeSpan.Zero;
            string fileName = $"TestDrive-Leads-{DateTime.UtcNow.FormatUtcDateTimeToUserFriendlyString(timeZoneOffset, "yyyyMMddHHmmss")}";
            string worksheetsName = "leads";
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                fileUrl = await ((ILeadDomainService)DomainService).ExportDataToExcelAsync(fileName, worksheetsName, timeZoneOffset, BuildRetrieveManyFilter(request), request.Sorting, (int)(page * pageSize), (int)pageSize);
            }
            return fileUrl;
        }

        private Expression<Func<Lead, bool>> BuildRetrieveManyFilter(LeadGetAllRequest request)
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
