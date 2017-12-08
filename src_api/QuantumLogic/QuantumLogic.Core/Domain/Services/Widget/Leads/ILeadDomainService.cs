using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Leads
{
    public interface ILeadDomainService : IEntityDomainService<Lead, int>
    {
        /// <summary>
        /// Is used to update entity is new property via domain rules
        /// </summary>        
        /// <param name="id">entity id</param>
        /// <param name="newValue">new value</param>
        /// <returns>task of updating entity property</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task ChangeIsNewAsync(int id, bool newValue);
        /// <summary>
        /// Is used to update entity is reached by manager property via domain rules
        /// </summary>        
        /// <param name="id">entity id</param>
        /// <param name="newValue">new value</param>
        /// <returns>task of updating entity property</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task ChangeIsReachedByManagerAsync(int id, bool newValue);
    }
}
