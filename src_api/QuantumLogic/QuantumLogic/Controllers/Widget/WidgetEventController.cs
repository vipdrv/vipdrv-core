using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.WidgetEvents;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.WidgetEvents;
using QuantumLogic.WebApi.DataModels.Requests;
using QuantumLogic.WebApi.DataModels.Requests.Widget.WidgetEvent;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/widget-event")]
    public class WidgetEventController : EntityController<WidgetEvent, int, WidgetEventDto, WidgetEventFullDto>
    {
        #region Ctors

        public WidgetEventController(IQLUnitOfWorkManager uowManager, IWidgetEventDomainService domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        #region CRUD

        [Authorize]
        [HttpGet("{id}")]
        public Task<WidgetEventFullDto> GetAsync(int id)
        {
            return InnerGetAsync(id);
        }
        [HttpPost]
        public Task<WidgetEventFullDto> CreateAsync([FromBody]WidgetEventFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [Authorize]
        [HttpPut]
        public Task<WidgetEventFullDto> UpdateAsync([FromBody]WidgetEventFullDto request)
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

        [HttpPatch("change-is-resolved/{id}")]
        public async Task ChangeIsResolvedAsync(int id, [FromBody]PatchBoolPropertyRequest request)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((IWidgetEventDomainService)DomainService).ChangeIsResolvedAsync(id, request.Value);
                await uow.CompleteAsync();
            }
        }

        #region Methods to operate with many entities

        [Authorize]
        [HttpPost("get-all/{page?}/{pageSize?}")]
        public Task<GetAllResponse<WidgetEventDto>> GetAllAsync([FromBody]WidgetEventGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<WidgetEvent, bool>> filter = (entity) =>
                (!request.SiteId.HasValue || request.SiteId.Value == entity.SiteId);
            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }

        #endregion
    }
}
