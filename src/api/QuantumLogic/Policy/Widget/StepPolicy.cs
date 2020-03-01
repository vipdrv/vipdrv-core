using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Exceptions.Policy;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class StepPolicy : EntityExtendedPolicy<Step, int>, IStepPolicy
    {
        #region Ctors

        public StepPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        protected override IQueryable<Step> InnerRetrieveAllFilter(IQueryable<Step> query)
        {
            return query;
        }

        protected override bool CanRetrieve(Step entity, bool throwEntityPolicyException)
        {
            bool result = InnerRetrieveAllFilter(new List<Step>() { entity }.AsQueryable()).Any();
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanCreate(Step entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllStep) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanCreateStep) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanUpdate(Step entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllStep) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateStep) ||
                Session.UserId.HasValue &&
                Session.UserId.Value == entity.Site.UserId &&
                (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) ||
                 PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateOwnStep));


            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanDelete(Step entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllStep) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteStep) ||
                Session.UserId.HasValue &&
                Session.UserId.Value == entity.Site.UserId &&
                (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteOwnStep));

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanChangeActivity(Step entity, bool throwEntityPolicyException)
        {
            return CanUpdate(entity, throwEntityPolicyException);
        }

        protected override bool CanChangeOrder(Step entity, bool throwEntityPolicyException)
        {
            return CanUpdate(entity, throwEntityPolicyException);
        }
    }
}
