using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Exceptions.Policy;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class ExpertPolicy : EntityExtendedPolicy<Expert, int>, IExpertPolicy
    {
        #region Ctors

        public ExpertPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        protected override IQueryable<Expert> InnerRetrieveAllFilter(IQueryable<Expert> query)
        {
            return query;
        }

        protected override bool CanRetrieve(Expert entity, bool throwEntityPolicyException)
        {
            bool result = InnerRetrieveAllFilter(new List<Expert>() { entity }.AsQueryable()).Any();
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanCreate(Expert entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllExpert) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanCreateExpert);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanUpdate(Expert entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllExpert) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateExpert) ||
                Session.UserId.HasValue &&
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateOwnExpert) &&
                Session.UserId.Value == entity.Site.UserId;

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanDelete(Expert entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllExpert) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteExpert) ||
                Session.UserId.HasValue &&
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteOwnExpert) && 
                Session.UserId.Value == entity.Site.UserId;

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanChangeActivity(Expert entity, bool throwEntityPolicyException)
        {
            return CanUpdate(entity, throwEntityPolicyException);
        }

        protected override bool CanChangeOrder(Expert entity, bool throwEntityPolicyException)
        {
            return CanUpdate(entity, throwEntityPolicyException);
        }
    }
}
