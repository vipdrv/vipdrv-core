using QuantumLogic.Core.Domain.Entities.MainModule;

namespace QuantumLogic.Core.Domain.Policy.Main
{
    public interface IInvitationPolicy : IEntityPolicy<Invitation, int>
    {
        /// <summary>Is used to check operation "Use" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <exception cref="Exceptions.Policy.EntityPolicyException">Thrown when this operation access is denied</exception>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        void PolicyUse(Invitation entity);
        /// <summary>Is used to check operation "Use" access via policy</summary>
        /// <param name="entity">entity for operation</param>
        /// <returns>check result (can this operation be provided for current entity or not)</returns>
        /// <exception cref="Exceptions.NotSupported.OperationIsNotSupportedException">Thrown when this operation is not supported for current entity</exception>
        bool CanUse(Invitation entity);
    }
}
