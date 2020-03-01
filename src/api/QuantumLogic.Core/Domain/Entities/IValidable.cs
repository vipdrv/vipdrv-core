namespace QuantumLogic.Core.Domain.Entities
{
    /// <summary>
    /// Is used to provide validation support for entity
    /// </summary>
    public interface IValidable
    {
        /// <summary>
        /// Is used to validate entity
        /// </summary>
        /// <returns>validation result</returns>
        bool IsValid();
        /// <summary>
        /// Is used to validate entity and throw exception if it is not valid
        /// <exception cref="Exceptions.Validation.ValidationException">Thrown when this entity is not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityPropertiesException">Thrown when this entity properties are not valid</exception>
        /// <exception cref="Exceptions.Validation.ValidateEntityRelationsException">Thrown when this entity relations are not valid</exception>
        /// </summary>
        void Validate();
    }
}
