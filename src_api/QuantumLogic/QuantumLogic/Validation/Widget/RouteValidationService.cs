using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;

namespace QuantumLogic.WebApi.Validation.Widget
{
    public class RouteValidationService : NullEntityValidationService<Route, int>, IRouteValidationService
    {
        #region Ctors

        public RouteValidationService()
            : base()
        { }

        #endregion
    }
}
