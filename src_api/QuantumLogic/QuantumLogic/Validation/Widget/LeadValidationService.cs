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
    }
}
