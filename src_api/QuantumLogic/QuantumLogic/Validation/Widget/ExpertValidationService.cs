using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;

namespace QuantumLogic.WebApi.Validation.Widget
{
    public class ExpertValidationService : NullEntityExtendedValidationService<Expert, int>, IExpertValidationService
    {
        #region Ctors

        public ExpertValidationService()
            : base()
        { }

        #endregion
    }
}
