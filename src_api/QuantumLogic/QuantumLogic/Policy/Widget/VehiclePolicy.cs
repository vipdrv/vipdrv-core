using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Exceptions.Policy;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.WebApi.Policy.Widget
{
    public class VehiclePolicy : EntityPolicy<Vehicle, int>, IVehiclePolicy
    {
        #region Ctors

        public VehiclePolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        public Expression<Func<Vehicle, bool>> GetRetrieveAllExpression()
        {
            return (entity) => true;
        }

        protected override IQueryable<Vehicle> InnerRetrieveAllFilter(IQueryable<Vehicle> query)
        {
            return query.Where(GetRetrieveAllExpression());
        }

        protected override bool CanRetrieve(Vehicle entity, bool throwEntityPolicyException)
        {
            bool result = InnerRetrieveAllFilter(new List<Vehicle>() { entity }.AsQueryable()).Any();
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanCreate(Vehicle entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllVehicle) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanCreateVehicle);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanUpdate(Vehicle entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllVehicle) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateVehicle) ||
                          Session.UserId.HasValue &&
                          Session.UserId.Value == entity.Site.UserId &&
                          (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) ||
                           PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateOwnVehicle));


            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanDelete(Vehicle entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllVehicle) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteVehicle) ||
                          Session.UserId.HasValue &&
                          Session.UserId.Value == entity.Site.UserId &&
                          (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) ||
                           PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteOwnVehicle));

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        public void PolicyImport(Site site)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllVehicle) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanImportVehicles) ||
                          Session.UserId.HasValue &&
                          Session.UserId.Value == site.UserId &&
                          (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) ||
                           PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanImportOwnVehicles));

            if (!result)
            {
                throw new EntityPolicyException();
            }
        }
    }
}
