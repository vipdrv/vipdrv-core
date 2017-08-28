using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class BeveragePolicy : NullEntityPolicy<Beverage, int>, IBeveragePolicy
    {
        #region Ctors

        public BeveragePolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion
    }
}
