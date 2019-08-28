using QuantumLogic.Core.Domain.Entities;

namespace QuantumLogic.Core.Domain.Policy
{
    /// <summary>
    /// Is used to provide access for specific entity operations
    /// </summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    /// <typeparam name="TPrimaryKey">type of primary key</typeparam>
    public interface IEntityExtendedPolicy<TEntity, TPrimaryKey> : IEntityPolicy<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IPassivable, IOrderable
    {
        /// <summary>Is used to check operation "ChangeActivity" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void PolicyChangeActivity(TEntity entity);
        /// <summary>Is used to check operation "ChangeActivity" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <returns>check result (can this operation be provided for current entity or not)</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool CanChangeActivity(TEntity entity);
        /// <summary>Is used to check operation "ChangeOrder" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void PolicyChangeOrder(TEntity entity);
        /// <summary>Is used to check operation "ChangeOrder" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <returns>check result (can this operation be provided for current entity or not)</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool CanChangeOrder(TEntity entity);
    }
}
