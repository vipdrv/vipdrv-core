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

        public void ValidateChangeContacts(Site entity)
        {
            ValidateChangeContacts(entity, true);
        }
        public bool IsValidChangeContacts(Site entity)
        {
            return ValidateChangeContacts(entity, false);
        }

        protected virtual bool ValidateChangeContacts(Site entity, bool throwValidationException)
        {
            return ValidateEntity(entity, throwValidationException);
        }
    }
}
