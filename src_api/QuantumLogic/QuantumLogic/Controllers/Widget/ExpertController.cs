using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Experts;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Experts;
using QuantumLogic.WebApi.DataModels.Requests;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Experts;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/expert")]
    public class ExpertController : EntityExtendedController<Expert, int, ExpertDto, ExpertFullDto>
    {
        #region Ctors

        public ExpertController(IQLUnitOfWorkManager uowManager, IExpertDomainService domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        #region CRUD

        [HttpGet("{id}")]
        public Task<ExpertFullDto> Get(int id)
        {
            return InnerGetAsync(id);
        }
        [HttpPost]
        public Task<ExpertFullDto> Create([FromBody]ExpertFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [HttpPut]
        public Task<ExpertFullDto> Update([FromBody]ExpertFullDto request)
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
        public Task<GetAllResponse<ExpertDto>> GetAll([FromBody]ExpertGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<Expert, bool>> filter = (entity) =>
               (!request.SiteId.HasValue || request.SiteId == entity.SiteId) &&
               (String.IsNullOrEmpty(request.SiteBeautyId) || request.SiteBeautyId == entity.Site.BeautyId);

            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }

        #endregion

        #region Extended metods

        [HttpPatch("change-activity/{id}")]
        public Task ChangeActivityAsync([FromBody]ChangeActivityRequest request, int id)
        {
            return ChangeActivityAsync(id, request.Value);
        }
        [HttpPatch("change-order/{id}")]
        public Task ChangeOrderAsync([FromBody]ChangeOrderRequest request, int id)
        {
            return ChangeOrderAsync(id, request.Value);
        }

        #endregion
    }
}
