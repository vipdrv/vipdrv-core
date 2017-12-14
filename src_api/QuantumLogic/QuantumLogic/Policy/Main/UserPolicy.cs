using System.Collections.Generic;
using System.Linq;
using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;
using QuantumLogic.Core.Exceptions.Policy;

namespace QuantumLogic.WebApi.Policy.Main
{
    public class UserPolicy : EntityPolicy<User, int>, IUserPolicy
    {
        #region Ctors

        public UserPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        protected override IQueryable<User> InnerRetrieveAllFilter(IQueryable<User> query)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllUser) || // CanAllOwn
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanRetrieveUser);

            if (result)
            {
                return query;
            }

            var resultOwn = Session.UserId.HasValue &&
                            (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn) ||
                             PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanRetrieveOwnUser));

            if (resultOwn)
            {
                query = query.Where(r => r.Id == Session.UserId);
            }
            else
            {
                query = query.DefaultIfEmpty();
            }

            return query;
        }

        protected override bool CanRetrieve(User entity, bool throwEntityPolicyException)
        {
            bool result = InnerRetrieveAllFilter(new List<User>() { entity }.AsQueryable()).Any();
            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }
            return result;
        }

        protected override bool CanCreate(User entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllUser) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanCreateUser) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanUpdate(User entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllUser) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateUser) ||
                Session.UserId.HasValue &&
                Session.UserId.Value == entity.Id &&
                (PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanUpdateOwnUser) ||
                PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllOwn));
                          

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }

        protected override bool CanDelete(User entity, bool throwEntityPolicyException)
        {
            bool result = PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllAll) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanAllUser) ||
                          PermissionChecker.IsGranted(QuantumLogicPermissionNames.CanDeleteUser);

            if (!result && throwEntityPolicyException)
            {
                throw new EntityPolicyException();
            }

            return result;
        }
    }
}
