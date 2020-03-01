using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;
using QuantumLogic.Core.Domain.Repositories.Main;
using QuantumLogic.Core.Domain.Validation.Main;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Main.Roles
{
    public class RoleDomainService : EntityDomainService<Role, int>, IRoleDomainService
    {
        #region Ctors

        public RoleDomainService(IRoleRepository repository, IRolePolicy policy, IRoleValidationService validationService)
            : base(repository, policy, validationService)
        { }

        #endregion

        protected override Task CascadeDeleteActionAsync(Role entity)
        {
            return Task.CompletedTask;
        }
        protected override Expression<Func<Role, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Role, object>>>()
            {

            }
            .ToArray();
        }
        protected override Expression<Func<Role, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<Role, object>>>()
            {

            }
            .ToArray();
        }
        internal override IEnumerable<LoadEntityRelationAction<Role>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Role>>();
        }
    }
}
