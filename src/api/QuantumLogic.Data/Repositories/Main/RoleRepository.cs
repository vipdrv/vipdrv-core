using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Repositories.Main;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Repositories.Main
{
    public class RoleRepository : EFRepository<Role, int>, IRoleRepository
    {
        #region Ctors

        public RoleRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public RoleRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion
    }
}
