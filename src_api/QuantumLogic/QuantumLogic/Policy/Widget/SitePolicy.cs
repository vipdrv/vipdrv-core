using System.Collections;
using System.Collections.Generic;
using System.Linq;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
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

        protected override IQueryable<Site> InnerRetrieveAllFilter(IQueryable<Site> query)
        {
            return query;
        }

        protected override bool CanRetrieve(Site entity, bool throwEntityPolicyException)
        {
            return true;
        }

        protected override bool CanCreate(Site entity, bool throwEntityPolicyException)
        {
            var result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                         PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllSite) ||
                         PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanCreateSite);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return true;
        }

        protected override bool CanUpdate(Site entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllSite) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateSite) ||
                          Session.UserId.HasValue &&
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateOwnSite) &&
                          Session.UserId.Value == entity.Id;

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanDelete(Site entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllSite) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteSite) ||
                          Session.UserId.HasValue &&
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteOwnSite) &&
                          Session.UserId.Value == entity.Id;

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }
    }
}
