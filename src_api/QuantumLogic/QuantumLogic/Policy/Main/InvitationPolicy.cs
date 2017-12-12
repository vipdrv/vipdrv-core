using System.Linq;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;

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
            throw new System.NotImplementedException();
        }

        protected override bool CanRetrieve(Invitation entity, bool throwEntityPolicyException)
        {
            throw new System.NotImplementedException();
        }

        protected override bool CanCreate(Invitation entity, bool throwEntityPolicyException)
        {
            throw new System.NotImplementedException();
        }

        protected override bool CanUpdate(Invitation entity, bool throwEntityPolicyException)
        {
            throw new System.NotImplementedException();
        }

        protected override bool CanDelete(Invitation entity, bool throwEntityPolicyException)
        {
            throw new System.NotImplementedException();
        }
    }
}
