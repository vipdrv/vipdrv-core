using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Repositories.Main;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Repositories.Main
{
    public class UserRepository : EFRepository<User, int>, IUserRepository
    {
        #region Ctors

        public UserRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public UserRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion
    }
}
