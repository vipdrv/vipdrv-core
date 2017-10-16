using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Routes;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Routes;
using QuantumLogic.WebApi.DataModels.Requests;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Routes;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Microsoft.AspNetCore.Mvc.Route("api/route")]
    public class RouteController : EntityExtendedController<Route, int, RouteDto, RouteFullDto>
    {
        #region Ctors

        public RouteController(IQLUnitOfWorkManager uowManager, IRouteDomainService domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        #region CRUD

        [HttpGet("{id}")]
        public Task<RouteFullDto> GetAsync(int id)
        {
            return InnerGetAsync(id);
        }
        [Authorize]
        [HttpPost]
        public Task<RouteFullDto> CreateAsync([FromBody]RouteFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [Authorize]
        [HttpPut]
        public Task<RouteFullDto> UpdateAsync([FromBody]RouteFullDto request)
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
        public Task<GetAllResponse<RouteDto>> GetAllAsync([FromBody]RouteGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<Route, bool>> filter = (entity) => request.SiteId.HasValue ? request.SiteId == entity.SiteId : true;
            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }

        #endregion

        #region Extended metods

        [Authorize]
        [HttpPatch("change-activity/{id}")]
        public Task ChangeActivityAsync(int id, [FromBody]ChangeActivityRequest request)
        {
            return ChangeActivityAsync(id, request.Value);
        }
        [Authorize]
        [HttpPatch("change-order/{id}")]
        public Task ChangeOrderAsync(int id, [FromBody]ChangeOrderRequest request)
        {
            return ChangeOrderAsync(id, request.Value);
        }

        #endregion
    }
}
