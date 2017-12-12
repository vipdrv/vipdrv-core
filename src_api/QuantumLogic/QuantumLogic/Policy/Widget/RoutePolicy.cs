using System.Collections.Generic;
using System.Linq;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Exceptions.Policy;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class RoutePolicy : EntityExtendedPolicy<Route, int>, IRoutePolicy
    {
        #region Ctors

        public RoutePolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        protected override IQueryable<Route> InnerRetrieveAllFilter(IQueryable<Route> query)
        {
            return query;
        }

        protected override bool CanRetrieve(Route entity, bool throwEntityPolicyException)
        {
            bool result = InnerRetrieveAllFilter(new List<Route>() { entity }.AsQueryable()).Any();
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanCreate(Route entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllRoute) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanCreateRoute);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanUpdate(Route entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllRoute) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateRoute) ||
                          Session.UserId.HasValue &&
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateOwnRoute) &&
                          Session.UserId.Value == entity.Site.UserId;

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanDelete(Route entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllRoute) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteRoute) ||
                          Session.UserId.HasValue &&
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteOwnRoute) &&
                          Session.UserId.Value == entity.Site.UserId;

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanChangeActivity(Route entity, bool throwEntityPolicyException)
        {
            return CanUpdate(entity, throwEntityPolicyException);
        }

        protected override bool CanChangeOrder(Route entity, bool throwEntityPolicyException)
        {
            return CanUpdate(entity, throwEntityPolicyException);
        }
    }
}
