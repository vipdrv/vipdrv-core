using QuantumLogic.Core.Domain.Entities;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services
{
    /// <summary>
    /// Is used to extend domain service for specific entities operations
    /// </summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    /// <typeparam name="TPrimaryKey">type of entity primary key</typeparam>
    public interface IEntityExtendedDomainService<TEntity, TPrimaryKey> : IEntityDomainService<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IPassivable, IOrderable
    {
        /// <summary>
        /// Is used to update entity activity via domain rules
        /// </summary>        
        /// <param name="id">entity id</param>
        /// <param name="newValue">new activity value</param>
        /// <returns>task of updating entity activity</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task ChangeActivityAsync(TPrimaryKey id, bool newValue);
        /// <summary>
        /// Is used to update entity order via domain rules
        /// </summary>        
        /// <param name="id">entity id</param>
        /// <param name="newValue">new order value</param>
        /// <returns>task of updating entity order</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task ChangeOrderAsync(TPrimaryKey id, int newValue);
        /// <summary>
        /// Is used to swap entity orders for two entities via domain rules
        /// </summary> 
        /// <param name="key1">first candidate key</param>
        /// <param name="key2">second candidate key</param>
        /// <returns>task of updating entity order</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task SwapOrdersAsync(TPrimaryKey key1, TPrimaryKey key2);

    }
}
