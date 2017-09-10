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

        [Microsoft.AspNetCore.Mvc.HttpGet("{id}")]
        public Task<RouteFullDto> Get(int id)
        {
            return InnerGetAsync(id);
        }
        [Microsoft.AspNetCore.Mvc.HttpPost]
        public Task<RouteFullDto> Create([Microsoft.AspNetCore.Mvc.FromBody]RouteFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [Microsoft.AspNetCore.Mvc.HttpPut]
        public Task<RouteFullDto> Update([Microsoft.AspNetCore.Mvc.FromBody]RouteFullDto request)
        {
            return InnerUpdateAsync(request);
        }
        [Microsoft.AspNetCore.Mvc.HttpDelete("{id}")]
        public Task Delete(int id)
        {
            return InnerDeleteAsync(id);
        }

        #endregion

        #region Custom methods

        [Microsoft.AspNetCore.Mvc.HttpPost("get-all/{page?}/{pageSize?}")]
        public Task<GetAllResponse<RouteDto>> GetAll([Microsoft.AspNetCore.Mvc.FromBody]RouteGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<Route, bool>> filter = (user) => true;
            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }

        #endregion

        #region Extended metods

        [HttpPatch("change-activity/{id}")]
        public Task ChangeActivityAsync(int id, [FromBody]ChangeActivityRequest request)
        {
            return ChangeActivityAsync(id, request.Value);
        }
        [HttpPatch("change-order/{id}")]
        public Task ChangeOrderAsync(int id, [FromBody]ChangeOrderRequest request)
        {
            return ChangeOrderAsync(id, request.Value);
        }

        #endregion
    }
}
