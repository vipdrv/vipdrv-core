using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;

namespace QuantumLogic.WebApi.Validation.Widget
{
    public class SiteValidationService : NullEntityValidationService<Site, int>, ISiteValidationService
    {
        #region Ctors

        public SiteValidationService()
            : base()
        { }

        #endregion
    }
}
