using QuantumLogic.Core.Domain.Entities.MainModule;
using System;
using System.Collections.Generic;
using System.Text;
using System.Linq.Expressions;
using System.Threading.Tasks;
using QuantumLogic.Core.Domain.Context;
using QuantumLogic.Core.Domain.Policy;
using QuantumLogic.Core.Domain.Repositories;
using QuantumLogic.Core.Domain.Validation;

namespace QuantumLogic.Core.Domain.Services.Main
{
    public class UserDomainService : EntityDomainService<User, int>, IUserDomainService
    {
        #region Ctors

        public UserDomainService(IDomainContext domainContext, IQLRepository<User, int> repository, IEntityPolicy<User, int> policy, IEntityValidationService<User, int> validationService)
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
