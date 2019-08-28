using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Repositories.Widget
{
    public class BeverageRepository : EFRepositoryX<Beverage, int>, IBeverageRepository
    {
        #region Ctors

        public BeverageRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public BeverageRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion
    }
}
