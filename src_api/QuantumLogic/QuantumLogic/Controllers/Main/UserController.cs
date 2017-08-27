using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Services;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Main;
using QuantumLogic.WebApi.DataModels.Requests.Main.Users;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Main
{
    [Route("api/user")]
    public class UserController : EntityController<User, int, UserDto>
    {
        #region Ctors

        public UserController(IQLUnitOfWorkManager uowManager, IEntityDomainService<User, int> domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        [HttpPost("get-all/{page?}/{pageSize?}")]
        public Task<GetAllResponse<UserDto>> GetAll([FromBody]UserGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<User, bool>> filter = (user) => true;
            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }
        [HttpGet("get/{id}")]
        public Task<UserDto> Get(int id)
        {
            return InnerGetAsync(id);
        }
        [HttpPost("create")]
        public Task<UserDto> Create([FromBody]UserDto request)
        {
            return InnerCreateAsync(request);
        }
        [HttpPut("update")]
        public Task<UserDto> Update([FromBody]UserDto request)
        {
            return InnerUpdateAsync(request);
        }
        [HttpDelete("delete/{id}")]
        public Task Delete(int id)
        {
            return InnerDeleteAsync(id);
        }
    }
}
