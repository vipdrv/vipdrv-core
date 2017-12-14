using System.Collections.Generic;
using System.Linq;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Exceptions.Policy;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class BeveragePolicy : EntityExtendedPolicy<Beverage, int>, IBeveragePolicy
    {
        #region Ctors

        public BeveragePolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        protected override IQueryable<Beverage> InnerRetrieveAllFilter(IQueryable<Beverage> query)
        {
            return query;
        }

        protected override bool CanRetrieve(Beverage entity, bool throwEntityPolicyException)
        {
            bool result = InnerRetrieveAllFilter(new List<Beverage>() { entity }.AsQueryable()).Any();
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanCreate(Beverage entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllBeverage) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanCreateBeverage) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanUpdate(Beverage entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllBeverage) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateBeverage) ||
                Session.UserId.HasValue &&
                Session.UserId.Value == entity.Site.UserId &&
                (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) ||
                 PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateOwnBeverage));


            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanDelete(Beverage entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllBeverage) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteBeverage) ||
                Session.UserId.HasValue &&
                Session.UserId.Value == entity.Site.UserId &&
                (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteOwnBeverage));

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanChangeActivity(Beverage entity, bool throwEntityPolicyException)
        {
            return CanUpdate(entity, throwEntityPolicyException);
        }

        protected override bool CanChangeOrder(Beverage entity, bool throwEntityPolicyException)
        {
            return CanUpdate(entity, throwEntityPolicyException);
        }
    }
}
