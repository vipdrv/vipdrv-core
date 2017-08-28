using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Services.Main.Users;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Main.Users;
using QuantumLogic.WebApi.DataModels.Requests.Main.Users;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Main
{
    [Route("api/user")]
    public class UserController : EntityController<User, int, UserDto, UserFullDto>
    {
        #region Ctors

        public UserController(IQLUnitOfWorkManager uowManager, IUserDomainService domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        #region CRUD

        [HttpGet("{id}")]
        public Task<UserFullDto> Get(int id)
        {
            return InnerGetAsync(id);
        }
        [HttpPost]
        public Task<UserFullDto> Create([FromBody]UserFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [HttpPut]
        public Task<UserFullDto> Update([FromBody]UserFullDto request)
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
        public Task<GetAllResponse<UserDto>> GetAll([FromBody]UserGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<User, bool>> filter = (user) => true;
            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }

        #endregion
    }
}
