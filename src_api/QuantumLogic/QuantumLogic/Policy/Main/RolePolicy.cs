using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;
using QuantumLogic.Core.Exceptions.Policy;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.WebApi.Policy.Main
{
    public class RolePolicy : EntityPolicy<Role, int>, IRolePolicy
    {
        #region Ctors

        public RolePolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        protected override IQueryable<Role> InnerRetrieveAllFilter(IQueryable<Role> query)
        {
            bool canGetAccessToManyEntities = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllRole) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanRetrieveRole);
            query = canGetAccessToManyEntities ? query : query.Where(r => false);
            return query;
        }

        protected override bool CanRetrieve(Role entity, bool throwEntityPolicyException)
        {
            bool result = InnerRetrieveAllFilter(new List<Role>() { entity }.AsQueryable()).Any();
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanCreate(Role entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllRole) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanCreateRole);
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanUpdate(Role entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllRole) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateRole);
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanDelete(Role entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllRole) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteRole);
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }
    }
}
