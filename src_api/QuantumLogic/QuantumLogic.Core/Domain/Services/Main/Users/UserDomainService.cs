using QuantumLogic.Core.Domain.Context;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;
using QuantumLogic.Core.Domain.Repositories.Main;
using QuantumLogic.Core.Domain.Services.Main.Users.Models;
using QuantumLogic.Core.Domain.Validation.Main;
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

        public UserDomainService(IDomainContext domainContext, IUserRepository repository, IUserPolicy policy, IUserValidationService validationService)
            : base(domainContext, repository, policy, validationService)
        { }

        #endregion

        public async Task<UserInfo> GetUserInfo(string login, string password, Func<User, string, bool> loginComparer)
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

        protected override Task CascadeDeleteActionAsync(User entity)
        {
            return Task.CompletedTask;
        }
        protected override IEnumerable<LoadEntityRelationAction<User>> GetLoadEntityRelationActions()
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
                entity => entity.Sites
            }
            .ToArray();
        }
    }
}
