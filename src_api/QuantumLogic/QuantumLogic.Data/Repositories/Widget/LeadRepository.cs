using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Repositories.Widget
{
    public class LeadRepository : EFRepository<Lead, int>, ILeadRepository
    {
        #region Ctors

        public LeadRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public LeadRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion
    }
}
