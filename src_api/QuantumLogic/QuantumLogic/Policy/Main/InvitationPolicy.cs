using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;

namespace QuantumLogic.WebApi.Policy.Main
{
    public class InvitationPolicy : NullEntityPolicy<Invitation, int>, IInvitationPolicy
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
    }
}
