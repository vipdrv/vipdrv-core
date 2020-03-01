using System;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.UnitOfWorks
{
    /// <summary>
    /// Is used as unit of work for QuantumLogic
    /// </summary>
    public interface IQLUnitOfWork : IDisposable
    {
        /// <summary>
        /// Is used to save changes to storage and commit transaction in transaction mode.
        /// </summary>
        /// <returns>task</returns>
        Task CompleteAsync();
        /// <summary>
        /// Is used to save changes to storage, but not commit transaction. If mode is not transactional throws exception.
        /// </summary>
        /// <returns>task</returns>
        Task PushChangesAsync();
    }
}
