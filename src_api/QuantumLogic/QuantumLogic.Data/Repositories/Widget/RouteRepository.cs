using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Repositories.Widget
{
    public class RouteRepository : EFRepository<Route, int>, IRouteRepository
    {
        #region Ctors

        public RouteRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public RouteRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion
    }
}
