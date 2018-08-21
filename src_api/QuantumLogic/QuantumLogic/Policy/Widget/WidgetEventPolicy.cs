using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Exceptions.Policy;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class WidgetEventPolicy : EntityPolicy<WidgetEvent, int>, IWidgetEventPolicy
    {
        #region Ctors

        public WidgetEventPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        protected override IQueryable<WidgetEvent> InnerRetrieveAllFilter(IQueryable<WidgetEvent> query)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllWidgetEvent);

            if (!result)
            {
                query = query.Where(r => false); ;
            }

            return query;
        }

        protected override bool CanRetrieve(WidgetEvent entity, bool throwEntityPolicyException)
        {
            bool result = InnerRetrieveAllFilter(new List<WidgetEvent>() { entity }.AsQueryable()).Any();
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanCreate(WidgetEvent entity, bool throwEntityPolicyException)
        {
            return true;
        }

        protected override bool CanUpdate(WidgetEvent entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllWidgetEvent);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanDelete(WidgetEvent entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllWidgetEvent);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }
    }
}
