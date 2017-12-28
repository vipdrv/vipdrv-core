using QuantumLogic.Core.Domain.Entities;
using System;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Repositories
{
    public interface IQLRepositoryX<TEntity, TPrimaryKey> : IQLRepository<TEntity, TPrimaryKey>
        where TEntity : IEntity<TPrimaryKey>, IPassivable, IOrderable
    {
        /// <summary>
        /// Is used to get max order from existed entities (via queryBuilder result)
        /// * returns null if no existed entities
        /// </summary>
        /// <param name="queryBuilder">query builder</param>
        /// <param name="includes">includes</param>
        /// <returns>max existing order or null if noone existed</returns>
        Task<int?> GetMaxExistedOrder(Func<IQueryable<TEntity>, IQueryable<TEntity>> queryBuilder, params Expression<Func<TEntity, object>>[] includes);
    }
}
