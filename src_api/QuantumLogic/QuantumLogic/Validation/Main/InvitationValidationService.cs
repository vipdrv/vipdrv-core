using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Validation.Main;

namespace QuantumLogic.WebApi.Validation.Main
{
    public class InvitationValidationService : NullEntityValidationService<Invitation, int>, IInvitationValidationService
    {
        #region Ctors

        public InvitationValidationService()
            : base()
        { }

        #endregion
    }
}
