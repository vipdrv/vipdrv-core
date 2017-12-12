using System.Collections.Generic;
using System.Linq;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Exceptions.Policy;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class LeadPolicy : EntityPolicy<Lead, int>, ILeadPolicy
    {
        #region Ctors

        public LeadPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        protected override IQueryable<Lead> InnerRetrieveAllFilter(IQueryable<Lead> query)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllLead) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanRetrieveLead);
            bool resultOwn = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanRetrieveOwnLead);

            if (!result && resultOwn)
            {
                query = query.Where(r => r.Site.Id == Session.UserId.Value);
            }

            if (!result && !resultOwn)
            {
                query = query.DefaultIfEmpty();
            }

            return query;
        }

        protected override bool CanRetrieve(Lead entity, bool throwEntityPolicyException)
        {
            bool result = InnerRetrieveAllFilter(new List<Lead>() { entity }.AsQueryable()).Any();
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanCreate(Lead entity, bool throwEntityPolicyException)
        {
            return true;
        }

        protected override bool CanUpdate(Lead entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllLead) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateLead) ||
                          Session.UserId.HasValue &&
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateOwnLead) &&
                          Session.UserId.Value == entity.Site.UserId;

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanDelete(Lead entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllLead) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteLead) ||
                          Session.UserId.HasValue &&
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteOwnLead) &&
                          Session.UserId.Value == entity.Site.UserId;

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }
    }
}
