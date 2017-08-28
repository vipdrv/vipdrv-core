using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;

namespace QuantumLogic.WebApi.Validation.Widget
{
    public class ExpertValidationService : NullEntityValidationService<Expert, int>, IExpertValidationService
    {
        #region Ctors

        public ExpertValidationService()
            : base()
        { }

        #endregion
    }
}
