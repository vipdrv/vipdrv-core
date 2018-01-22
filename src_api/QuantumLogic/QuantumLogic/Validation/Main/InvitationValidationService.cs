using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Validation.Main;
using QuantumLogic.Core.Exceptions.Validation;

namespace QuantumLogic.WebApi.Validation.Main
{
    public class InvitationValidationService : NullEntityValidationService<Invitation, int>, IInvitationValidationService
    {
        #region Ctors

        public InvitationValidationService()
            : base()
        { }

        #endregion

        protected override bool ValidateCreate(Invitation entity, bool throwValidationException)
        {
            if (!entity.Role.CanBeUsedForInvitation && throwValidationException)
            {
                throw new ValidationException();
            }
            return entity.Role.CanBeUsedForInvitation && base.ValidateCreate(entity, throwValidationException);
        }
        protected override bool ValidateUpdate(Invitation oldEntity, Invitation actualEntity, bool throwValidationException)
        {
            if (!actualEntity.Role.CanBeUsedForInvitation && throwValidationException)
            {
                throw new ValidationException();
            }
            return actualEntity.Role.CanBeUsedForInvitation && base.ValidateUpdate(oldEntity, actualEntity, throwValidationException);
        }
    }
}
