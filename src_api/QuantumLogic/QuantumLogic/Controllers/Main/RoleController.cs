using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Services.Main.Roles;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Main.Roles;
using QuantumLogic.WebApi.DataModels.Requests.Main.Roles;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Main
{
    [Route("api/role")]
    public class RoleController : EntityController<Role, int, RoleDto, RoleFullDto>
    {
        #region Ctors

        public RoleController(IQLUnitOfWorkManager uowManager, IRoleDomainService domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        #region CRUD

        [HttpGet("{id}")]
        public Task<RoleFullDto> GetAsync(int id)
        {
            return InnerGetAsync(id);
        }
        [Authorize]
        [HttpPost]
        public Task<RoleFullDto> CreateAsync([FromBody]RoleFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [Authorize]
        [HttpPut]
        public Task<RoleFullDto> UpdateAsync([FromBody]RoleFullDto request)
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
        public Task<GetAllResponse<RoleDto>> GetAllAsync([FromBody]RoleGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<Role, bool>> filter = (entity) =>
                (String.IsNullOrEmpty(request.Name) || !String.IsNullOrEmpty(entity.Name) && entity.Name.ToUpper().Contains(request.Name.ToUpper())) &&
                (!request.CanBeUsedForInvitation.HasValue || request.CanBeUsedForInvitation.Value == entity.CanBeUsedForInvitation);
            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }

        #endregion
    }
}
