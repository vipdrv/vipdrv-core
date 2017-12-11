using System.Linq;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Exceptions.Policy;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class ExpertPolicy : EntityExtendedPolicy<Expert, int>, IExpertPolicy
    {
        #region Ctors

        public ExpertPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        protected override IQueryable<Expert> InnerRetrieveAllFilter(IQueryable<Expert> query)
        {
            throw new System.NotImplementedException();
        }

        protected override bool CanRetrieve(Expert entity, bool throwEntityPolicyException)
        {
            return true;
        }

        protected override bool CanCreate(Expert entity, bool throwEntityPolicyException)
        {
            throw new System.NotImplementedException();
        }

        protected override bool CanUpdate(Expert entity, bool throwEntityPolicyException)
        {
            throw new System.NotImplementedException();
        }

        protected override bool CanDelete(Expert entity, bool throwEntityPolicyException)
        {
            throw new System.NotImplementedException();
        }

        protected override bool CanChangeActivity(Expert entity, bool throwEntityPolicyException)
        {
            throw new System.NotImplementedException();
        }

        protected override bool CanChangeOrder(Expert entity, bool throwEntityPolicyException)
        {
            throw new System.NotImplementedException();
        }
    }
}
