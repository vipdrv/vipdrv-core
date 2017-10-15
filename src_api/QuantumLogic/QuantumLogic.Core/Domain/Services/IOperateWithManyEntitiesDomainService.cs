using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services
{
    /// <summary>
    /// Is used to provide operations via domain rules for many entities
    /// </summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    public interface IOperateWithManyEntitiesDomainService<TEntity>
    {
        /// <summary>
        /// Is used to get total count of query entities via filter
        /// </summary>
        /// <param name="filter">filter as expression</param>
        /// <returns>total count of filtered entities</returns>
        Task<int> GetTotalCountAsync(Expression<Func<TEntity, bool>> filter = null);
        /// <summary>
        /// Is used to retrieve all entities via domain rules
        /// </summary>
        /// <param name="filter">filter as expression</param>
        /// <param name="sorting">sorting as string</param>
        /// <param name="skip">skip count</param>
        /// <param name="take">take count</param>
        /// <returns>retrieved entities</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        Task<IList<TEntity>> RetrieveAllAsync(Expression<Func<TEntity, bool>> filter = null, string sorting = null, int skip = 0, int take = 0);
    }
}
