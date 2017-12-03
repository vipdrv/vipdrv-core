using Microsoft.AspNetCore.Authorization;
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
        public Task<BeverageFullDto> GetAsync(int id)
        {
            return InnerGetAsync(id);
        }
        [Authorize]
        [HttpPost]
        public Task<BeverageFullDto> CreateAsync([FromBody]BeverageFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [Authorize]
        [HttpPut]
        public Task<BeverageFullDto> UpdateAsync([FromBody]BeverageFullDto request)
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
        public Task<GetAllResponse<BeverageDto>> GetAllAsync([FromBody]BeverageGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<Beverage, bool>> filter = (entity) =>
                (!request.UserId.HasValue || request.UserId.Value == entity.Site.UserId) &&
                (!request.SiteId.HasValue || request.SiteId.Value == entity.SiteId);
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
