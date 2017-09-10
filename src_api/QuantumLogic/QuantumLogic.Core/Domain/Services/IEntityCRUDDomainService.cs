using QuantumLogic.Core.Domain.Entities;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services
{
    /// <summary>
    /// Is used to provide CRUD (Create|Retrieve|Update|Delete) operations via domain rules for entity
    /// </summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    /// <typeparam name="TPrimaryKey">type of entity primary key</typeparam>
    public interface IEntityCRUDDomainService<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>
    {
        /// <summary>
        /// Is used to retrieve entity via domain rules
        /// </summary>
        /// <param name="id">entity id</param>
        /// <returns>task of retrieving entity with retrieved entity as result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        Task<TEntity> RetrieveAsync(TPrimaryKey id);
        /// <summary>
        /// Is used to create entity via domain rules
        /// </summary>
        /// <param name="entity">entity to create</param>
        /// <returns>task of creating entity with created entity as result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task<TEntity> CreateAsync(TEntity entity);
        /// <summary>
        /// Is used to update entity via domain rules
        /// </summary>
        /// <param name="entity">entity to update</param>
        /// <returns>task of updating entity with updated entity as result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task<TEntity> UpdateAsync(TEntity entity);
        /// <summary>
        /// Is used to delete entity via domain rules
        /// </summary>
        /// <param name="id">entity id</param>
        /// <returns>task of deleting entity</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task DeleteAsync(TPrimaryKey id);
    }
}
