using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Services.Main.Invitations;
using QuantumLogic.Core.Domain.Services.Main.Users;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Main.Invitations;
using QuantumLogic.WebApi.DataModels.Dtos.Main.Users;
using QuantumLogic.WebApi.DataModels.Requests.Main.Users;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Main
{
    [Route("api/user")]
    public class UserController : EntityController<User, int, UserDto, UserFullDto>
    {
        #region Injected dependencies

        protected IInvitationDomainService InvitationDomainService { get; private set; }

        #endregion

        #region Ctors

        public UserController(IQLUnitOfWorkManager uowManager, IUserDomainService domainService, IInvitationDomainService invitationDomainService)
            : base(uowManager, domainService)
        {
            InvitationDomainService = invitationDomainService;
        }

        #endregion

        #region CRUD

        [Authorize]
        [HttpGet("{id}")]
        public Task<UserFullDto> GetAsync(int id)
        {
            return InnerGetAsync(id);
        }
        //[HttpPost]
        //public Task<UserFullDto> CreateAsync([FromBody]UserFullDto request)
        //{
        //    return InnerCreateAsync(request);
        //}
        //[HttpPut]
        //public Task<UserFullDto> UpdateAsync([FromBody]UserFullDto request)
        //{
        //    return InnerUpdateAsync(request);
        //}
        //[HttpDelete("{id}")]
        //public Task DeleteAsync(int id)
        //{
        //    return InnerDeleteAsync(id);
        //}

        #endregion

        #region Methods to operate with many entities

        [Authorize]
        [HttpPost("get-all/{page?}/{pageSize?}")]
        public Task<GetAllResponse<UserDto>> GetAllAsync([FromBody]UserGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<User, bool>> filter = (entity) => true;
            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }

        #endregion

        #region Special methods

        [Authorize]
        [HttpGet("{id}/invitation/{page?}/{pageSize?}/{sorting?}")]
        public async Task<GetAllResponse<InvitationDto>> GetAllCreatedInvitationsAsync(int id, uint page = 0, uint pageSize = 0, string sorting = null)
        {
            Expression<Func<Invitation, bool>> filter = (entity) => entity.InvitatorId == id;
            int totalCount;
            IList<Invitation> entities;
            int skip = (int)(page * pageSize);
            int take = (int)pageSize;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                entities = await InvitationDomainService.RetrieveAllAsync(filter, sorting, skip, take);
                totalCount = take > 0 || skip > 0 && (entities.Count == take || skip != 0) ? 
                    await InvitationDomainService.GetTotalCountAsync(filter) : 
                    entities.Count;
            }
            List<InvitationDto> entityDtos = new List<InvitationDto>(entities.Count);
            foreach (Invitation entity in entities)
            {
                InvitationDto entityDto = new InvitationDto();
                entityDto.MapFromEntity(entity);
                entityDto.NormalizeAsResponse();
                entityDtos.Add(entityDto);
            }
            return new GetAllResponse<InvitationDto>(entityDtos, totalCount);
        }

        [Authorize]
        [HttpPost("{id}/invitation")]
        public async Task<InvitationDto> CreateInvitationAsync([FromBody]InvitationDto request, int id)
        {
            Invitation entity = request.MapToEntity();
            entity.InvitatorId = id;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                entity = await InvitationDomainService.CreateAsync(request.MapToEntity());
                await uow.CompleteAsync();
            }
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                entity = await InvitationDomainService.RetrieveAsync(entity.Id);
            }
            InvitationDto dto = new InvitationDto();
            dto.MapFromEntity(entity);
            return dto;
        }

        [HttpPost("{invitation-code}")]
        public async Task RegisterAsync([FromBody]UserFullDto request, string invitationCode)
        {
            if (request == null)
            {
                throw new ArgumentNullException(nameof(request));
            }
            request.NormalizeAsRequest();
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await InvitationDomainService.UseInvitationAsync(invitationCode);
                await DomainService.CreateAsync(request.MapToEntity());
                await uow.CompleteAsync();
            }
        }

        #endregion
    }
}
