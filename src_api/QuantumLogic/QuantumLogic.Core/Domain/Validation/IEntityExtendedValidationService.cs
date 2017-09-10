using QuantumLogic.Core.Domain.Entities;

namespace QuantumLogic.Core.Domain.Validation
{
    /// <summary>
    /// Is used to provide validation for specific entity operations
    /// </summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    /// <typeparam name="TPrimaryKey">type of primary key</typeparam>
    public interface IEntityExtendedValidationService<TEntity, TPrimaryKey> : IEntityValidationService<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IPassivable, IOrderable
    {
        /// <summary>Is used to validate entity for operation "ChangeActivity"</summary>
        /// <param name="entity">entity to validate</param>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void ValidateChangeActivity(TEntity entity);
        /// <summary>Is used to check is current entity valid for operation "ChangeActivity" or not</summary>
        /// <param name="entity">entity to validate</param>
        /// <returns>validation result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool IsValidChangeActivity(TEntity entity);
        /// <summary>Is used to validate entity for operation "ChangeOrder"</summary>
        /// <param name="entity">entity to validate</param>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void ValidateChangeOrder(TEntity entity);
        /// <summary>Is used to check is current entity valid for operation "ChangeOrder" or not</summary>
        /// <param name="entity">entity to validate</param>
        /// <returns>validation result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool IsValidChangeOrder(TEntity entity);
    }
}
