using QuantumLogic.Core.Domain.Context;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;
using QuantumLogic.Core.Domain.Repositories.Main;
using QuantumLogic.Core.Domain.Validation.Main;
using System;
using System.Collections.Generic;
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

        protected override Task CascadeDeleteAction(User entity)
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
