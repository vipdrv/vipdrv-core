using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Domain.Validation.Widget
{
    public interface ILeadValidationService : IEntityValidationService<Lead, int>
    {
        /// <summary>Is used to check is current entity valid for operation "ChangeIsNew" or not</summary>
        /// <param name="entity">entity to validate</param>
        /// <returns>validation result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool IsValidChangeIsNew(Lead entity);
        void ValidateChangeIsNew(Lead entity);
        /// <summary>Is used to check is current entity valid for operation "ChangeIsReachedByManager" or not</summary>
        /// <param name="entity">entity to validate</param>
        /// <returns>validation result</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool IsValidChangeIsReachedByManager(Lead entity);
        void ValidateChangeIsReachedByManager(Lead entity);
    }
}
