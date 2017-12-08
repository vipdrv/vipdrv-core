using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;

namespace QuantumLogic.WebApi.Validation.Widget
{
    public class LeadValidationService : NullEntityValidationService<Lead, int>, ILeadValidationService
    {
        #region Ctors

        public LeadValidationService()
            : base()
        { }

        #endregion

        public bool IsValidChangeIsNew(Lead entity)
        {
            return ValidateChangeIsNew(entity, false);
        }
        public bool IsValidChangeIsReachedByManager(Lead entity)
        {
            return ValidateChangeIsReachedByManager(entity, false);
        }
        public void ValidateChangeIsNew(Lead entity)
        {
            ValidateChangeIsNew(entity, true);
        }
        public void ValidateChangeIsReachedByManager(Lead entity)
        {
            ValidateChangeIsReachedByManager(entity, true);
        }

        #region Helpers

        protected virtual bool ValidateChangeIsReachedByManager(Lead entity, bool throwValidationException)
        {
            return true;
        }
        protected virtual bool ValidateChangeIsNew(Lead entity, bool throwValidationException)
        {
            return true;
        }

        #endregion
    }
}
