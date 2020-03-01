using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Exceptions.Policy;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class SitePolicy : EntityPolicy<Site, int>, ISitePolicy
    {
        #region Ctors

        public SitePolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        public IQueryable<Site> ImportVehiclesAllFilter(IQueryable<Site> query)
        {
            query = (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) || PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanImportVehicles)) ? 
                query :
                (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) || PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanImportOwnVehicles)) && Session.UserId.HasValue ? 
                    query.Where(r => r.UserId == Session.UserId.Value) :
                    query.Where(r => false);
            return query;
        }

        public void PolicyImportVehicles(Site entity)
        {
            CanImportVehicles(entity, true);
        }

        public bool CanImportVehicles(Site entity)
        {
            return CanImportVehicles(entity, false);
        }

        #region Helpers

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
                         PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) ||
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
                          Session.UserId.Value == entity.UserId &&
                          (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) ||
                           PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateOwnSite));

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
                          Session.UserId.Value == entity.UserId &&
                          (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) ||
                           PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteOwnSite));

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected virtual bool CanImportVehicles(Site entity, bool throwEntityPolicyException)
        {
            bool result = ImportVehiclesAllFilter(new List<Site>() { entity }.AsQueryable()).Any();
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        #endregion
    }
}
