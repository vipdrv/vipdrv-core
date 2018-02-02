using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Beverages;
using QuantumLogic.Core.Domain.Services.Widget.Experts;
using QuantumLogic.Core.Domain.Services.Widget.Routes;
using QuantumLogic.Core.Domain.Services.Widget.Sites;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Beverages;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Experts;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Routes;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Sites;
using QuantumLogic.WebApi.DataModels.Requests;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Sites;
using QuantumLogic.WebApi.DataModels.Responses;
using QuantumLogic.WebApi.DataModels.Responses.Widget.Site;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/site")]
    public class SiteController : EntityController<Site, int, SiteDto, SiteFullDto>
    {
        #region Injected dependencies

        protected readonly IBeverageDomainService BeverageDomainService;
        protected readonly IExpertDomainService ExpertDomainService;
        protected readonly IRouteDomainService RouteDomainService;

        #endregion

        #region Ctors

        public SiteController(IQLUnitOfWorkManager uowManager, ISiteDomainService domainService, IBeverageDomainService beverageDomainService, IExpertDomainService expertDomainService, IRouteDomainService routeDomainService)
            : base(uowManager, domainService)
        {
            BeverageDomainService = beverageDomainService;
            ExpertDomainService = expertDomainService;
            RouteDomainService = routeDomainService;
        }

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
        /// <summary>
        /// Is used to get all agregated info related to site (can be retrieved via other endpoints but used to improve performance)
        /// </summary>
        /// <param name="id">site id</param>
        /// <returns>agregated site info</returns>
        [HttpGet("{id}/agregated-info")]
        public async Task<SiteAgregatedInfoDto> GetSiteAregatedInfoAsync(int id)
        {
            int defaultSkip = 0;
            int defaultTake = 100;
            string defaultSorting = "order asc";
            Site siteEntity;
            IList<Beverage> beverageEntities;
            IList<Expert> expertEntities;
            IList<Route> routeEntities;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                Task<Site> getSiteEntityTask = DomainService.RetrieveAsync(id);
                Task<IList<Beverage>> getBeverageEntitiesTask = BeverageDomainService.RetrieveAllAsync((entity) => entity.IsActive && entity.SiteId == id, defaultSorting, defaultSkip, defaultTake);
                Task<IList<Expert>> getExpertEntitiesTask = ExpertDomainService.RetrieveAllAsync((entity) => entity.IsActive && entity.SiteId == id, defaultSorting, defaultSkip, defaultTake);
                Task<IList<Route>> getRouteEntitiesTask = RouteDomainService.RetrieveAllAsync((entity) => entity.IsActive && entity.SiteId == id, defaultSorting, defaultSkip, defaultTake);
                await Task.WhenAll(getSiteEntityTask, getBeverageEntitiesTask, getExpertEntitiesTask, getRouteEntitiesTask);
                siteEntity = await getSiteEntityTask;
                beverageEntities = await getBeverageEntitiesTask;
                expertEntities = await getExpertEntitiesTask;
                routeEntities = await getRouteEntitiesTask;
            }
            SiteFullDto siteDto = new SiteFullDto();
            siteDto.MapFromEntity(siteEntity);
            siteDto.NormalizeAsResponse();
            return new SiteAgregatedInfoDto(
                siteDto,
                MapEntitiesToDtos<Beverage, BeverageDto>(beverageEntities),
                MapEntitiesToDtos<Expert, ExpertDto>(expertEntities),
                MapEntitiesToDtos<Route, RouteDto>(routeEntities));
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
