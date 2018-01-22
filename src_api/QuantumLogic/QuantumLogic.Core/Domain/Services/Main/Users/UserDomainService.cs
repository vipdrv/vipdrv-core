using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;
using QuantumLogic.Core.Domain.Repositories.Main;
using QuantumLogic.Core.Domain.Services.Main.Users.Models;
using QuantumLogic.Core.Domain.Validation.Main;
using QuantumLogic.Core.Exceptions.Authorization;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Main.Users
{
    public class UserDomainService : EntityDomainService<User, int>, IUserDomainService
    {
        #region Ctors

        public UserDomainService(IUserRepository repository, IUserPolicy policy, IUserValidationService validationService)
            : base(repository, policy, validationService)
        { }

        #endregion

        public async Task<UserInfo> GetUserInfoAsync(string login, string password, Func<User, string, bool> loginComparer)
        {
            User user = await ((IUserRepository)Repository)
                .FirstOrDefaultWithDeepIncludesAsync((entity) => loginComparer(entity, login) && entity.PasswordHash == password);
            return user == null ?
                null :
                new UserInfo(
                    user.Id.ToString(),
                    user.Id,
                    user.Username,
                    user.UserRoles.Select(r => r.Role.Name).ToList(),
                    user.UserClaims.Select(r => r.Claim.Id).Union(user.UserRoles.SelectMany(r => r.Role.RoleClaims.Select(e => e.Claim.Id))).OrderBy(r => r).ToList(),
                    user.AvatarUrl);
        }

        public Task<bool> IsUsernameValidAsync(string value)
        {
            return Repository
                .FirstOrDefaultAsync(r => String.Equals(r.Username, value, StringComparison.OrdinalIgnoreCase))
                .ContinueWith((pt) => pt.Result == null);
        }

        public async Task UpdatePersonalInfoAsync(int userId, string newFirstName, string newSecondName, string newEmail, string newPhoneNumber)
        {
            User entity = await Repository.GetAsync(userId);
            Policy.PolicyUpdate(entity);
            entity.FirstName = newFirstName;
            entity.SecondName = newSecondName;
            entity.Email = newEmail;
            entity.PhoneNumber = newPhoneNumber;
            ValidationService.ValidateEntity(entity);
            await Repository.UpdateAsync(entity);
        }
        public async Task UpdatePasswordAsync(int userId, string oldPassword, string newPassword)
        {
            User entity = await Repository.FirstOrDefaultAsync(r => r.Id == userId && r.PasswordHash == oldPassword);
            if (entity != null)
            {
                entity.PasswordHash = newPassword;
                await Repository.UpdateAsync(entity);
            }
            else
            {
                throw new PasswordIsNotValidException();
            }
        }
        public async Task UpdateAvatarAsync(int userId, string newAvatarUrl)
        {
            User entity = await Repository.GetAsync(userId);
            Policy.PolicyUpdate(entity);
            entity.AvatarUrl = newAvatarUrl;
            ValidationService.ValidateEntity(entity);
            await Repository.UpdateAsync(entity);
        }

        protected override Task CascadeDeleteActionAsync(User entity)
        {
            return Task.CompletedTask;
        }
        internal override IEnumerable<LoadEntityRelationAction<User>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<User>>();
        }
        protected override Expression<Func<User, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<User, object>>>()
            {
                entity => entity.Sites
            }
            .ToArray();
        }
        protected override Expression<Func<User, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<User, object>>>()
            {
                entity => entity.Sites,
                entity => entity.UserRoles
            }
            .ToArray();
        }
    }
}
