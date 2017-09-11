using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class ExpertPolicy : NullEntityExtendedPolicy<Expert, int>, IExpertPolicy
    {
        #region Ctors

        public ExpertPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion
    }
}
