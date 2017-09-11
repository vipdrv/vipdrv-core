using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Sites;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Sites;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Sites;
using QuantumLogic.WebApi.DataModels.Responses;
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
        public Task<SiteFullDto> Get(int id)
        {
            return InnerGetAsync(id);
        }
        [HttpPost]
        public Task<SiteFullDto> Create([FromBody]SiteFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [HttpPut]
        public Task<SiteFullDto> Update([FromBody]SiteFullDto request)
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
        public Task<GetAllResponse<SiteDto>> GetAll([FromBody]SiteGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<Site, bool>> filter = (entity) => true;
            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }

        #endregion
    }
}
