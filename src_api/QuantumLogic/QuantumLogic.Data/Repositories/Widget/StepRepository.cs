using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Repositories.Widget
{
    public class StepRepository : EFRepositoryX<Step, int>, IStepRepository
    {
        #region Ctors

        public StepRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public StepRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion
    }
}
