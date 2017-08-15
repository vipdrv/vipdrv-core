using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Policy;
using System.Linq;

namespace QuantumLogic.WebApi.Policy
{
    public abstract class EntityPolicy<TEntity, TPrimaryKey> : IEntityPolicy<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>
    {
        #region Injected Dependencies

        public IQLPermissionChecker PermissionChecker { get; private set; }
        public IQLSession Session { get; private set; }

        #endregion

        #region Ctors

        public EntityPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
        {
            PermissionChecker = permissionChecker;
            Session = session;
        }

        #endregion

        public IQueryable<TEntity> RetrieveAllFilter(IQueryable<TEntity> query)
        {
            return InnerRetrieveAllFilter(query);
        }
        public void PolicyRetrieve(TEntity entity)
        {
            CanRetrieve(entity, true);
        }
        public void PolicyCreate(TEntity entity)
        {
            CanCreate(entity, true);
        }
        public void PolicyUpdate(TEntity entity)
        {
            CanUpdate(entity, true);
        }
        public void PolicyDelete(TEntity entity)
        {
            CanDelete(entity, true);
        }
        public bool CanRetrieve(TEntity entity)
        {
            return CanRetrieve(entity, false);
        }
        public bool CanCreate(TEntity entity)
        {
            return CanCreate(entity, false);
        }
        public bool CanUpdate(TEntity entity)
        {
            return CanUpdate(entity, false);
        }
        public bool CanDelete(TEntity entity)
        {
            return CanDelete(entity, false);
        }

        #region Inner policy methods

        protected abstract IQueryable<TEntity> InnerRetrieveAllFilter(IQueryable<TEntity> query);
        protected abstract bool CanRetrieve(TEntity entity, bool throwEntityPolicyException);
        protected abstract bool CanCreate(TEntity entity, bool throwEntityPolicyException);
        protected abstract bool CanUpdate(TEntity entity, bool throwEntityPolicyException);
        protected abstract bool CanDelete(TEntity entity, bool throwEntityPolicyException);

        #endregion
    }
}
