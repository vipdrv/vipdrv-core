using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Domain.Policy.WidgetModule
{
    public interface ISitePolicy : IEntityPolicy<Site, int>
    {
        /// <summary>Is used to check operation "ChangeContacts" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void PolicyChangeContacts(Site entity);
        /// <summary>Is used to check operation "ChangeContacts" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <returns>check result (can this operation be provided for current entity or not)</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool CanChangeContacts(Site entity);
    }
}
