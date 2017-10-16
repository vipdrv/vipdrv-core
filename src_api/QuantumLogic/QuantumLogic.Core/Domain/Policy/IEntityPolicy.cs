using QuantumLogic.Core.Domain.Entities;
using System.Linq;

namespace QuantumLogic.Core.Domain.Policy
{
    /// <summary>Is used to manage access to entity operations</summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    /// <typeparam name="TPrimaryKey">type of primary key</typeparam>
    public interface IEntityPolicy<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>
    {
        /// <summary>Is used to modify query with policy rules</summary>
        /// <param name="query">query to modify</param>
        /// <returns>modified query by policy</returns>
        /// <exception cref="Core.Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        IQueryable<TEntity> RetrieveAllFilter(IQueryable<TEntity> query);
        /// <summary>Is used to check operation "Retrieve" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Core.Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void PolicyRetrieve(TEntity entity);
        /// <summary>Is used to check operation "Create" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Core.Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void PolicyCreate(TEntity entity);
        /// <summary>Is used to check operation "Update" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Core.Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void PolicyUpdate(TEntity entity);
        /// <summary>Is used to check operation "Delete" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Core.Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void PolicyDelete(TEntity entity);
        /// <summary>Is used to check operation "Retrieve" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <returns>check result (can this operation be provided for current entity or not)</returns>
        /// <exception cref="Core.Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool CanRetrieve(TEntity entity);
        /// <summary>Is used to check operation "Create" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <returns>check result (can this operation be provided for current entity or not)</returns>
        /// <exception cref="Core.Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool CanCreate(TEntity entity);
        /// <summary>Is used to check operation "Update" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <returns>check result (can this operation be provided for current entity or not)</returns>
        /// <exception cref="Core.Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool CanUpdate(TEntity entity);
        /// <summary>Is used to check operation "Delete" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <returns>check result (can this operation be provided for current entity or not)</returns>
        /// <exception cref="Core.Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool CanDelete(TEntity entity);
    }
}
