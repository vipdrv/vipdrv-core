using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Sites;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Sites;
using QuantumLogic.WebApi.DataModels.Requests;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Sites;
using QuantumLogic.WebApi.DataModels.Responses;
using QuantumLogic.WebApi.DataModels.Responses.Widget.Site;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/site")]
    public class SiteController : EntityController<Site, int, SiteDto, SiteFullDto>
    {
        #region Ctors

        public SiteController(IQLUnitOfWorkManager uowManager, ISiteDomainService domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        #region CRUD

        [HttpGet("{id}")]
        public Task<SiteFullDto> GetAsync(int id)
        {
            return InnerGetAsync(id);
        }
        [Authorize]
        [HttpPost]
        public Task<SiteFullDto> CreateAsync([FromBody]SiteFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [Authorize]
        [HttpPut]
        public Task<SiteFullDto> UpdateAsync([FromBody]SiteFullDto request)
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

        [HttpPost("get-all/{page?}/{pageSize?}")]
        public Task<GetAllResponse<SiteDto>> GetAllAsync([FromBody]SiteGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<Site, bool>> filter = (entity) =>
                (!request.Id.HasValue || request.Id.Value == entity.Id) &&
                (!request.UserId.HasValue || request.UserId.Value == entity.UserId) &&
                (String.IsNullOrEmpty(request.Dealer) || !String.IsNullOrEmpty(entity.DealerName) && entity.DealerName.ToUpper().Contains(request.Dealer.ToUpper())) &&
                (String.IsNullOrEmpty(request.Name) || !String.IsNullOrEmpty(entity.Name) && entity.Name.ToUpper().Contains(request.Name.ToUpper()));
            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }

        #endregion

        #region Special methods

        [HttpGet("{id}/week-schedule")]
        public async Task<SiteWeekSchedule> GetWeekScheduleAsync(int id)
        {
            SiteWeekSchedule schedule;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                schedule = new SiteWeekSchedule(id, await ((ISiteDomainService)DomainService).RetrieveWeekSchedule(id));
            }
            return schedule;
        }
        [Authorize]
        [HttpPatch("change-contacts/{id}")]
        public async Task ChangeContactsAsync(int id, [FromBody]ChangeContactsRequest request)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((ISiteDomainService)DomainService).ChangeContactsAsync(id, request.Value);
                await uow.CompleteAsync();
            }
        }
        [Authorize]
        [HttpPatch("change-use-expert-step/{id}")]
        public async Task ChangeUseExpertStepAsync(int id, [FromBody]ChangeActivityRequest request)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((ISiteDomainService)DomainService).ChangeUseExpertStepAsync(id, request.Value);
                await uow.CompleteAsync();
            }
        }
        [Authorize]
        [HttpPatch("change-use-beverage-step/{id}")]
        public async Task ChangeUseBeverageStepAsync(int id, [FromBody]ChangeActivityRequest request)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((ISiteDomainService)DomainService).ChangeUseBeverageStepAsync(id, request.Value);
                await uow.CompleteAsync();
            }
        }
        [Authorize]
        [HttpPatch("change-use-route-step/{id}")]
        public async Task ChangeUseRouteStepAsync(int id, [FromBody]ChangeActivityRequest request)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((ISiteDomainService)DomainService).ChangeUseRouteStepAsync(id, request.Value);
                await uow.CompleteAsync();
            }
        }

        #endregion
    }
}
