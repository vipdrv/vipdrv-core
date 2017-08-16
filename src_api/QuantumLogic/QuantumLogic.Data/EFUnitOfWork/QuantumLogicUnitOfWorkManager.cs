using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.EFUnitOfWork
{
    public sealed class QuantumLogicUnitOfWorkManager  : IQLUnitOfWorkManager
    {
        #region Fields

        private static object _syncRoot = new object();
        private static QuantumLogicUnitOfWork _currentUow;

        #endregion

        #region Injected dependencies

        private DbContextManager ContextManager { get; set; }

        #endregion

        public IQLUnitOfWork Current
        {
            get
            {
                return _currentUow;
            }
        }

        #region Ctors

        public QuantumLogicUnitOfWorkManager(DbContextManager contextManager)
        {
            ContextManager = contextManager;
        }

        #endregion

        public IQLUnitOfWork CurrentOrCreateNew(bool useTransaction = true)
        {
            bool stub;
            return CurrentOrCreateNew(out stub, useTransaction);
        }

        public IQLUnitOfWork CurrentOrCreateNew(out bool newCreated, bool useTransaction = true)
        {
            lock (_syncRoot)
            {
                if (_currentUow == null)
                {
                    newCreated = true;
                    _currentUow = new QuantumLogicUnitOfWork(ContextManager, useTransaction, DisposeUnitOfWork);
                }
                else
                {
                    newCreated = false;
                }
                return _currentUow;
            }
        }

        #region Helpers

        private void DisposeUnitOfWork(QuantumLogicUnitOfWork uow)
        {
            lock (_syncRoot)
            {
                ContextManager.DisposeContext();
            }
        }

        #endregion
    }
}
