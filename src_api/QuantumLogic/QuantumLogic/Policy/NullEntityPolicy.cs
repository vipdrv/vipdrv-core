using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities;
using System.Linq;

namespace QuantumLogic.WebApi.Policy
{
    /// <summary>
    /// Is used as null (no deny) policy for entity operations
    /// </summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    /// <typeparam name="TPrimaryKey">type of primary key</typeparam>
    public class NullEntityPolicy<TEntity, TPrimaryKey> : EntityPolicy<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>
    {
        #region Ctors

        public NullEntityPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion

        #region Inner policy methods null (no deny access) implementation

        protected override bool CanCreate(TEntity entity, bool throwEntityPolicyException)
        {
            return true;
        }
        protected override bool CanDelete(TEntity entity, bool throwEntityPolicyException)
        {
            return true;
        }
        protected override bool CanRetrieve(TEntity entity, bool throwEntityPolicyException)
        {
            return true;
        }
        protected override bool CanUpdate(TEntity entity, bool throwEntityPolicyException)
        {
            return true;
        }
        protected override IQueryable<TEntity> InnerRetrieveAllFilter(IQueryable<TEntity> query)
        {
            return query;
        }

        #endregion
    }
}
