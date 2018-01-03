using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Repositories.Widget
{
    public class ExpertRepository : EFRepositoryX<Expert, int>, IExpertRepository
    {
        #region Ctors

        public ExpertRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public ExpertRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion
    }
}
