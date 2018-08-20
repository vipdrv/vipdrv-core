using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;

namespace QuantumLogic.WebApi.Validation.Widget
{
    public class WidgetEventValidationService : NullEntityValidationService<WidgetEvent, int>, IWidgetEventValidationService
    {
        #region Ctors

        public WidgetEventValidationService()
            : base()
        { }

        #endregion
    }
}
