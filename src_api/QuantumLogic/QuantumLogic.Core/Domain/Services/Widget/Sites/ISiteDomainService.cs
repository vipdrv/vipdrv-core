using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Scheduling.Week;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Sites
{
    public interface ISiteDomainService : IEntityDomainService<Site, int>
    {
        /// <summary>
        /// Is used to update entity contacts via domain rules
        /// </summary>        
        /// <param name="id">entity id</param>
        /// <param name="newValue">new contacts value</param>
        /// <returns>task of updating entity contacts</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task ChangeContactsAsync(int id, string newValue);

        /// <summary>
        /// Is used to retrieve week schedule for site (merge of schedules of all active experts)
        /// </summary>
        Task<IList<DayOfWeekInterval>> RetrieveWeekSchedule(int id);
        /// <summary>
        /// Is used to update entity use expert step property via domain rules
        /// </summary>        
        /// <param name="id">entity id</param>
        /// <param name="newValue">new use expert step value</param>
        /// <returns>task of updating entity use expert step</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task ChangeUseExpertStepAsync(int id, bool newValue);
        /// <summary>
        /// Is used to update entity use beverage step property via domain rules
        /// </summary>        
        /// <param name="id">entity id</param>
        /// <param name="newValue">new use beverage step value</param>
        /// <returns>task of updating entity use beverage step</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task ChangeUseBeverageStepAsync(int id, bool newValue);
        /// <summary>
        /// Is used to update entity use route step property via domain rules
        /// </summary>        
        /// <param name="id">entity id</param>
        /// <param name="newValue">new use route step value</param>
        /// <returns>task of updating entity use route step</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        Task ChangeUseRouteStepAsync(int id, bool newValue);
    }
}
