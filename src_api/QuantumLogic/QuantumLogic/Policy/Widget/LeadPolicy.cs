using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class LeadPolicy : NullEntityPolicy<Lead, int>, ILeadPolicy
    {
        #region Ctors

        public LeadPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion
    }
}
