using QuantumLogic.Core.Domain.Entities;

namespace QuantumLogic.Core.Domain.Validation
{
    /// <summary>Is used to manage validation (validation facade) for entity operations</summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    /// <typeparam name="TPrimaryKey">type of primary key</typeparam>
    public interface IEntityValidationService<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>
    {
        /// <summary>Is used to validate entity</summary>
        /// <param name="entity">entity to validate</param>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void ValidateEntity(TEntity entity);
        /// <summary>Is used to validate entity for operation "Create"</summary>
        /// <param name="entity">entity to validate</param>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void ValidateCreate(TEntity entity);
        /// <summary>Is used to validate entity for operation "Update"</summary>
        /// <param name="oldEntity">current entity to validate in operation</param>
        /// <param name="actualEntity">actual entity to validate in operation</param>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        /// <exception cref="Core.Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void ValidateUpdate(TEntity oldEntity, TEntity actualEntity);
        /// <summary>Is used to validate entity for operation "Delete"</summary>
        /// <param name="entity">entity to validate</param>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void ValidateDelete(TEntity entity);
        /// <summary>Is used to check is current entity valid or not</summary>
        /// <param name="entity">entity to validate</param>
        /// <returns>validation result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool IsValidEntity(TEntity entity);
        /// <summary>Is used to check is current entity valid for operation "Create" or not</summary>
        /// <param name="entity">entity to validate</param>
        /// <returns>validation result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool IsValidCreate(TEntity entity);
        /// <summary>Is used to check is current entity valid for operation "Update" or not</summary>
        /// <param name="oldEntity">current entity to validate in operation</param>
        /// <param name="actualEntity">actual entity to validate in operation</param>
        /// <returns>validation result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool IsValidUpdate(TEntity oldEntity, TEntity actualEntity);
        /// <summary>Is used to check is current entity valid for operation "Delete" or not</summary>
        /// <param name="entity">entity to validate</param>
        /// <returns>validation result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool IsValidDelete(TEntity entity);
    }
}
