using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Repositories.Widget
{
    public class WidgetEventRepository : EFRepository<WidgetEvent, int>, IWidgetEventRepository
    {
        #region Ctors

        public WidgetEventRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public WidgetEventRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion
    }
}
