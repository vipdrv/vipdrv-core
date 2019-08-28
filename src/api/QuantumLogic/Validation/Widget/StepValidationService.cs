using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;

namespace QuantumLogic.WebApi.Validation.Widget
{
    public class StepValidationService : NullEntityExtendedValidationService<Step, int>, IStepValidationService
    {
        #region Ctors

        public StepValidationService()
            : base()
        { }

        #endregion
    }
}
