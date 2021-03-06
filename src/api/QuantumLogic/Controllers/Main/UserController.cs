﻿using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Services.Main.Invitations;
using QuantumLogic.Core.Domain.Services.Main.Users;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.Core.Exceptions.Authorization;
using QuantumLogic.Core.Exceptions.Validation;
using QuantumLogic.Core.Utils.Email;
using QuantumLogic.Core.Utils.Email.Data.Templates;
using QuantumLogic.WebApi.DataModels.Dtos.Main.Invitations;
using QuantumLogic.WebApi.DataModels.Dtos.Main.Users;
using QuantumLogic.WebApi.DataModels.Requests.Main.Users;
using QuantumLogic.WebApi.DataModels.Responses;
using SendGrid.Helpers.Mail;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Net;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Main
{
    [Route("api/user")]
    public class UserController : EntityController<User, int, UserDto, UserFullDto>
    {
        #region Injected dependencies

        protected readonly IInvitationDomainService InvitationDomainService;
        protected readonly ITestDriveEmailService TestDriveEmailService;

        #endregion

        #region Ctors

        public UserController(IQLUnitOfWorkManager uowManager, IUserDomainService domainService, IInvitationDomainService invitationDomainService, ITestDriveEmailService testDriveEmailService)
            : base(uowManager, domainService)
        {
            InvitationDomainService = invitationDomainService;
            TestDriveEmailService = testDriveEmailService;
        }

        #endregion

        #region CRUD

        [Authorize]
        [HttpGet("{id}")]
        public Task<UserFullDto> GetAsync(int id)
        {
            return InnerGetAsync(id);
        }
        [HttpPost]
        public Task<UserFullDto> CreateAsync([FromBody]UserFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [HttpPut]
        public Task<UserFullDto> UpdateAsync([FromBody]UserFullDto request)
        {
            return InnerUpdateAsync(request);
        }
        [HttpDelete("{id}")]
        public Task DeleteAsync(int id)
        {
            return InnerDeleteAsync(id);
        }

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
        [HttpPost("invitation")]
        public async Task<InvitationDto> CreateInvitationAsync([FromBody]InvitationDto request)
        {
            string origin;
            try
            {
                Microsoft.Extensions.Primitives.StringValues origins;
                Request.Headers.TryGetValue("Origin", out origins);
                origin = origins[0];
            }
            catch
            {
                Response.StatusCode = (int)HttpStatusCode.ServiceUnavailable;
                throw new ArgumentException("Origin");
            }

            Invitation entity;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                entity = await InvitationDomainService.CreateAsync(request.MapToEntity());
                await uow.CompleteAsync();
            }
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                entity = await InvitationDomainService.RetrieveAsync(entity.Id);
            }

            SendGrid.Response sendDealerInvitationEmailResponse = await TestDriveEmailService
                .SendDealerInvitationEmail(
                    new EmailAddress(entity.Email, string.Empty), 
                    new DealerInvitationEmailTemplate($"{origin}/#/registration/{entity.InvitationCode}"));

            InvitationDto dto = new InvitationDto();
            dto.MapFromEntity(entity);
            return dto;
        }

        [Authorize]
        [HttpDelete("invitation/{id}")]
        public async Task DeleteInvitationAsync(int id)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await InvitationDomainService.DeleteAsync(id);
                await uow.CompleteAsync();
            }
        }

        [HttpPost("{invitationCode}")]
        public async Task RegisterAsync([FromBody]UserFullDto request, string invitationCode)
        {
            if (request == null)
            {
                throw new ArgumentNullException(nameof(request));
            }
            request.NormalizeAsRequest();
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                if (!(await ((IUserDomainService)DomainService).IsUsernameValidAsync(request.Username)))
                {
                    throw new ValidateEntityPropertiesException(nameof(request.Username));
                }
                Invitation invitation = await InvitationDomainService.UseInvitationAsync(invitationCode);
                User newUser = request.MapToEntity();
                newUser.MaxSitesCount = invitation.AvailableSitesCount;
                newUser.UserRoles = new List<UserRole>() { new UserRole() { User = newUser, RoleId = invitation.RoleId } };
                await DomainService.CreateAsync(newUser);
                await uow.CompleteAsync();
            }
        }

        [HttpGet("is-username-valid/{value}")]
        public Task<bool> IsUsernameValid(string value)
        {
            return ((IUserDomainService)DomainService).IsUsernameValidAsync(value);
        }

        [Authorize]
        [HttpPatch("{id}/patch-password")]
        public async Task PatchPassword([FromBody]PatchPasswordRequest request, int id)
        {
            try
            {
                using (var uow = UowManager.CurrentOrCreateNew(true))
                {
                    await ((IUserDomainService)DomainService)
                        .UpdatePasswordAsync(id, request.OldPassword, request.NewPassword);
                    await uow.CompleteAsync();
                }
            }
            catch (PasswordIsNotValidException)
            {
                Response.StatusCode = (int)HttpStatusCode.Unauthorized;
            }
        }

        [Authorize]
        [HttpPatch("{id}/patch-avatar")]
        public async Task PatchPassword([FromBody]PatchAvatarRequest request, int id)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((IUserDomainService)DomainService)
                    .UpdateAvatarAsync(id, request.NewAvatarUrl);
                await uow.CompleteAsync();
            }
        }

        [Authorize]
        [HttpPatch("{id}/patch-personal-info")]
        public async Task PatchPersonalInfo([FromBody]PatchPersonalInfoRequest request, int id)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((IUserDomainService)DomainService)
                    .UpdatePersonalInfoAsync(id, request.FirstName, request.SecondName, request.Email, request.PhoneNumber);
                await uow.CompleteAsync();
            }
        }

        #endregion
    }
}
