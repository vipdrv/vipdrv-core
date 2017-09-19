using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Domain.Validation.Widget
{
    public interface ISiteValidationService : IEntityValidationService<Site, int>
    {
        /// <summary>Is used to validate entity for operation "ChangeContacts"</summary>
        /// <param name="entity">entity to validate</param>
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void ValidateChangeContacts(Site entity);
        /// <summary>Is used to check is current entity valid for operation "ChangeContacts" or not</summary>
        /// <param name="entity">entity to validate</param>
        /// <returns>validation result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool IsValidChangeContacts(Site entity);
    }
}
