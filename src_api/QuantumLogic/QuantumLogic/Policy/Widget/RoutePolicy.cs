using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class RoutePolicy : NullEntityExtendedPolicy<Route, int>, IRoutePolicy
    {
        #region Ctors

        public RoutePolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion
    }
}
