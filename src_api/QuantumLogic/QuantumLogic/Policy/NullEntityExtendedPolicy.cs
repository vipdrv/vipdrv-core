using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Policy;

namespace QuantumLogic.WebApi.Policy
{
    public class NullEntityExtendedPolicy<TEntity, TPrimaryKey> : NullEntityPolicy<TEntity, TPrimaryKey>, IEntityExtendedPolicy<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IPassivable, IOrderable
    {
        #region Ctors

        public NullEntityExtendedPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        public void PolicyChangeActivity(TEntity entity)
        {
            CanChangeActivity(entity, true);
        }
        public bool CanChangeActivity(TEntity entity)
        {
            return CanChangeActivity(entity, false);
        }
        public void PolicyChangeOrder(TEntity entity)
        {
            CanChangeOrder(entity, true);
        }
        public bool CanChangeOrder(TEntity entity)
        {
            return CanChangeOrder(entity, false);
        }

        #region Inner policy methods null (no deny access) implementation

        protected bool CanChangeActivity(TEntity entity, bool throwEntityPolicyException)
        {
            return true;
        }
        protected bool CanChangeOrder(TEntity entity, bool throwEntityPolicyException)
        {
            return true;
        }

        #endregion
    }
}
