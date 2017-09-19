using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class SitePolicy : NullEntityPolicy<Site, int>, ISitePolicy
    {
        #region Ctors

        public SitePolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        public void PolicyChangeContacts(Site entity)
        {
            CanChangeContacts(entity, true);
        }
        public bool CanChangeContacts(Site entity)
        {
            return CanChangeContacts(entity, false);
        }

        protected virtual bool CanChangeContacts(Site entity, bool throwEntityPolicyException)
        {
            return true;
        }
    }
}
