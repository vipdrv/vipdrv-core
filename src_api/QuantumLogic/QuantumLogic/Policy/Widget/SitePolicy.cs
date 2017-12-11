using System.Collections;
using System.Collections.Generic;
using System.Linq;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Exceptions.Policy;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class SitePolicy : EntityPolicy<Site, int>, ISitePolicy
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

        protected override IQueryable<Site> InnerRetrieveAllFilter(IQueryable<Site> query)
        {
            long userId = 123;
            IList<string> userPermissions = new List<string>() { "canCreateSite", "canUpdateSite" };

            string constantPermission1 = "canAllSite";
            string constantPermission2 = "canRetrieveAll";
            string constantPermission3 = "canRetrieveSite";
            string constantPermission4 = "canRetrieveOwnSites";

            bool result = userPermissions.Contains(constantPermission1) ||
                          userPermissions.Contains(constantPermission2) ||
                          userPermissions.Contains(constantPermission3);

            if (result == false)
            {
                return Enumerable.Empty<Site>().AsQueryable();
            }

            if (userPermissions.Contains(constantPermission4))
            {
                return query.Where(r => r.UserId == userId);
            }

            return query;
        }

        protected override bool CanRetrieve(Site entity, bool throwEntityPolicyException)
        {
            return true;

            if (!InnerRetrieveAllFilter(new List<Site>() { entity }.AsQueryable()).Any())
            {
                if (throwEntityPolicyException)
                {
                    throw new EntityPolicyException();
                }
                return false;
            }
        }

        protected override bool CanCreate(Site entity, bool throwEntityPolicyException)
        {
            string permission = "carCreateSite";

            IList<string> userPermissions = new List<string>() { "canCreateSite", "canUpdateSite" };

            bool result = userPermissions.Contains(permission);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanUpdate(Site entity, bool throwEntityPolicyException)
        {
            throw new System.NotImplementedException();
        }

        protected override bool CanDelete(Site entity, bool throwEntityPolicyException)
        {
            throw new System.NotImplementedException();
        }
    }
}
