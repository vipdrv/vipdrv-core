using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Policy;
using QuantumLogic.Core.Authorization;

namespace QuantumLogic.WebApi.Policy
{
    public abstract class EntityExtendedPolicy<TEntity, TPrimaryKey> : EntityPolicy<TEntity, TPrimaryKey>, IEntityExtendedPolicy<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IPassivable, IOrderable
    {
        #region Ctors

        public EntityExtendedPolicy(IQLPermissionChecker permissionChecker, IQLSession session) 
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

        #region Inner policy

        protected abstract bool CanChangeActivity(TEntity entity, bool throwEntityPolicyException);
        protected abstract bool CanChangeOrder(TEntity entity, bool throwEntityPolicyException);

        #endregion
    }
}
