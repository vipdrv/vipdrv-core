using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Storage;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.Data.EFContext;
using System;
using System.Threading.Tasks;

namespace QuantumLogic.Data.EFUnitOfWork
{
    public class QuantumLogicUnitOfWork : IQLUnitOfWork
    {
        #region Fields

        private DbContextManager _contextManager;
        private IDbContextTransaction _currentTransaction;
        private Action<QuantumLogicUnitOfWork> _disposeAction;
        private bool _isTransactionCommited = false;

        #endregion

        #region Ctors

        public QuantumLogicUnitOfWork(DbContextManager contextManager, bool useTransaction, Action<QuantumLogicUnitOfWork> disposeAction)
        {
            _contextManager = contextManager;
            _disposeAction = disposeAction;
            _contextManager.BuildOrCurrentContext();
            if (useTransaction)
            {
                _currentTransaction = _contextManager.CurrentContext.Database.BeginTransaction();
            }
        }

        #endregion

        public async Task CompleteAsync()
        {
            await _contextManager.CurrentContext.SaveChangesAsync();
            if (_currentTransaction != null)
            {
                _isTransactionCommited = true;
                _currentTransaction.Commit();
            }
        }

        public async Task PushChangesAsync()
        {
            if (_currentTransaction == null)
                throw new Exception("Push to database in not transaction mode is not avaliable. Try set useTransaction=true");
            await _contextManager.CurrentContext.SaveChangesAsync();
        }

        public void Dispose()
        {
            if (_currentTransaction != null)
            {
                if (!_isTransactionCommited)
                {
                    _currentTransaction.Rollback();
                }
                _currentTransaction.Dispose();
            }
            _disposeAction(this);
        }
    }
}
