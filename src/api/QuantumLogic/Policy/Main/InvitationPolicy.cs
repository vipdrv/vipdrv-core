using System.Collections.Generic;
using System.Linq;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;
using QuantumLogic.Core.Exceptions.Policy;

namespace QuantumLogic.WebApi.Policy.Main
{
    public class InvitationPolicy : EntityPolicy<Invitation, int>, IInvitationPolicy
    {
        #region Ctors

        public InvitationPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        public void PolicyUse(Invitation entity)
        {
            CanUse(entity, true);
        }
        public bool CanUse(Invitation entity)
        {
            return CanUse(entity, false);
        }

        protected virtual bool CanUse(Invitation entity, bool throwEntityPolicyException)
        {
            return true;
        }

        protected override IQueryable<Invitation> InnerRetrieveAllFilter(IQueryable<Invitation> query)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllInvitation) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanRetrieveInvitation);
            query = result ? query : query.Where(r => false);
            return query;
        }

        protected override bool CanRetrieve(Invitation entity, bool throwEntityPolicyException)
        {
            bool result = InnerRetrieveAllFilter(new List<Invitation>() { entity }.AsQueryable()).Any();
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanCreate(Invitation entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllInvitation) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanCreateInvitation);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanUpdate(Invitation entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllInvitation) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateInvitation);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanDelete(Invitation entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllInvitation) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteInvitation);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }
    }
}
