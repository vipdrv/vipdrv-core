using System.Linq;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Exceptions.Policy;
using QuantumLogic.Data.EFContext;

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
            return true;
        }

        protected override bool CanCreate(Expert entity, bool throwEntityPolicyException)
        {
            var result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                         PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllExpert) ||
                         PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanCreateExpert);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return true;
        }

        protected override bool CanUpdate(Expert entity, bool throwEntityPolicyException)
        {
            var result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                         PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllExpert) ||
                         PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateExpert);

            if (result || (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateOwnExpert) && EntityBelongsToUserSite(Session.UserId, entity.SiteId)))
            {
                return true;
            }

            if (throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return false;
        }

        protected override bool CanDelete(Expert entity, bool throwEntityPolicyException)
        {
            var result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                         PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllExpert) ||
                         PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteExpert);

            if (result || (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteOnwExpert) && EntityBelongsToUserSite(Session.UserId, entity.SiteId)))
            {
                return true;
            }

            if (throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return false;
        }

        protected override bool CanChangeActivity(Expert entity, bool throwEntityPolicyException)
        {
            return CanUpdate(entity, throwEntityPolicyException);
        }

        protected override bool CanChangeOrder(Expert entity, bool throwEntityPolicyException)
        {
            return CanUpdate(entity, throwEntityPolicyException);
        }

        private bool EntityBelongsToUserSite(long? userId, int siteId)
        {
            if (siteId == new QuantumLogicDbContext().Sites.SingleOrDefault(r => r.UserId == userId).Id)
            {
                return true;
            }
            return false;
        }
    }
}
