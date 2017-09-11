using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Beverages;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Beverages;
using QuantumLogic.WebApi.DataModels.Requests;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Beverages;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/beverage")]
    public class BeverageController : EntityExtendedController<Beverage, int, BeverageDto, BeverageFullDto>
    {
        #region Ctors

        public BeverageController(IQLUnitOfWorkManager uowManager, IBeverageDomainService domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        #region CRUD

        [HttpGet("{id}")]
        public Task<BeverageFullDto> Get(int id)
        {
            return InnerGetAsync(id);
        }
        [HttpPost]
        public Task<BeverageFullDto> Create([FromBody]BeverageFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [HttpPut]
        public Task<BeverageFullDto> Update([FromBody]BeverageFullDto request)
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
        public Task<GetAllResponse<BeverageDto>> GetAll([FromBody]BeverageGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<Beverage, bool>> filter = (entity) => request.SiteId.HasValue ? request.SiteId == entity.SiteId : true;
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
