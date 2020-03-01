using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Repositories.Widget
{
    public class SiteRepository : EFRepository<Site, int>, ISiteRepository
    {
        #region Ctors

        public SiteRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public SiteRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion
    }
}
