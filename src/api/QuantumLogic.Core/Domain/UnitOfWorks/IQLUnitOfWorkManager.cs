namespace QuantumLogic.Core.Domain.UnitOfWorks
{
    /// <summary>
    /// Is used to manage unit of works <see cref="IUnitOfWork"/>
    /// </summary>
    public interface IQLUnitOfWorkManager
    {
        /// <summary>
        /// Is used to return current unit of work. If unit of work was not started returns null.
        /// </summary>
        IQLUnitOfWork Current { get; }
        /// <summary>
        /// Is used to get current or create new unit of work.
        /// </summary>
        /// <param name="useTransaction">use transaction mode</param>
        /// <returns>unit of work</returns>
        IQLUnitOfWork CurrentOrCreateNew(bool useTransaction = true);
        /// <summary>
        /// Is used to get current or create new unit of work. 
        /// </summary>
        /// <param name="newCreated">was new unit of work created or not</param>
        /// <param name="useTransaction">use transaction mode</param>
        /// <returns>unit of work</returns>
        IQLUnitOfWork CurrentOrCreateNew(out bool newCreated, bool useTransaction = true);
    }
}
