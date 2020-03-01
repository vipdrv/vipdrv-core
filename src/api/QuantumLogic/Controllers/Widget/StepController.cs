using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Steps;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Steps;
using QuantumLogic.WebApi.DataModels.Requests;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Steps;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/step")]
    public class StepController : EntityExtendedController<Step, int, StepDto, StepFullDto>
    {
        #region Ctors

        public StepController(IQLUnitOfWorkManager uowManager, IStepDomainService domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        #region CRUD

        [HttpGet("{id}")]
        public Task<StepFullDto> GetAsync(int id)
        {
            return InnerGetAsync(id);
        }
        [Authorize]
        [HttpPost]
        public Task<StepFullDto> CreateAsync([FromBody]StepFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [Authorize]
        [HttpPut]
        public Task<StepFullDto> UpdateAsync([FromBody]StepFullDto request)
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
        public Task<GetAllResponse<StepDto>> GetAllAsync([FromBody]StepGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<Step, bool>> filter = (entity) =>
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
        [Authorize]
        [HttpPatch("swap-orders")]
        public Task SwapOrdersAsync([FromBody]SwapOrdersRequest<int> request)
        {
            return SwapOrdersAsync(request.Key1, request.Key2);
        }

        #endregion
    }
}
