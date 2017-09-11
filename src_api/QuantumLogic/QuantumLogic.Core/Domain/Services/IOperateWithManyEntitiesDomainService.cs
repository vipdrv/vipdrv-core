using QuantumLogic.Core.Domain.Services.Models;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services
{
    /// <summary>
    /// Is used to provide operations via domain rules for many entities
    /// </summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    /// <typeparam name="TPrimaryKey">type of entity primary key</typeparam>
    public interface IOperateWithManyEntitiesDomainService<TEntity, TPrimaryKey>
    {
        /// <summary>
        /// Is used to retrieve all entities via domain rules
        /// </summary>
        /// <param name="filter">filter as expression</param>
        /// <param name="sorting">sorting as string</param>
        /// <param name="skip">skip count</param>
        /// <param name="take">take count</param>
        /// <returns>object with retrieved entities and total count of existed entities</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        Task<RetrieveAllResultModel<TEntity>> RetrieveAllAsync(Expression<Func<TEntity, bool>> filter = null, string sorting = null, int skip = 0, int take = 0);
    }
}
