using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Repositories.Main;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Repositories.Main
{
    public class InvitationRepository : EFRepository<Invitation, int>, IInvitationRepository
    {
        #region Ctors

        public InvitationRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public InvitationRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion
    }
}
