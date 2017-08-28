using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Leads;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Leads;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/lead")]
    public class LeadController : EntityController<Lead, int, LeadDto, LeadFullDto>
    {
        #region Ctors

        public LeadController(IQLUnitOfWorkManager uowManager, ILeadDomainService domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        #region CRUD

        [HttpGet("{id}")]
        public Task<LeadFullDto> Get(int id)
        {
            return InnerGetAsync(id);
        }
        [HttpPost]
        public Task<LeadFullDto> Create([FromBody]LeadFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [HttpPut]
        public Task<LeadFullDto> Update([FromBody]LeadFullDto request)
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
        public Task<GetAllResponse<LeadDto>> GetAll([FromBody]LeadGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<Lead, bool>> filter = (user) => true;
            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }

        #endregion
    }
}
