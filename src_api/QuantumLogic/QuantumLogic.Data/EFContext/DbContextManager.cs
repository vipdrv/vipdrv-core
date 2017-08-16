using Microsoft.EntityFrameworkCore;
using System;

namespace QuantumLogic.Data.EFContext
{
    public class DbContextManager
    {
        #region Fields

        private DbContext _context;
        private object _syncRoot = new object();

        #endregion

        #region Injected dependencies

        protected IServiceProvider ServiceProvider { get; private set; }

        #endregion

        #region Ctors

        public DbContextManager(IServiceProvider provider)
        {
            ServiceProvider = provider;
        }

        #endregion

        /// <summary>
        /// Return current DB context asociated with current thread context. If current DBContext is not set then thow exception
        /// </summary>
        public DbContext CurrentContext
        {
            get
            {
                var result = _context;
                if (result == null)
                {
                    throw new Exception("With the current logical flow is not associated DBcontext. Use BuildContext() first.");
                }
                else
                {
                    return result;
                }
            }
        }

        /// <summary>
        /// Build new DB context and bind it to cuurent thead context. If DBContext already esist throw exception.
        /// </summary>
        public void BuildContext()
        {
            lock (_syncRoot)
            {
                if (_context != null)
                    throw new Exception("With the current flow is already associated logical data context.");
                else
                    _context = CreateContext();
            }
        }

        /// <summary>
        /// Dispose current thread associated context. 
        /// </summary>
        public void DisposeContext()
        {
            lock (_syncRoot)
            {
                if (_context == null)
                    throw new Exception("With the current logical flow is not associated DBcontext. Nothing to dispose");
                else
                {
                    _context.Dispose();
                    _context = null;
                }
            }
        }

        /// <summary>
        /// If DbContext exist in current thread context, then return it else create new context and associate it with thead.
        /// </summary>
        /// <returns></returns>
        public DbContext BuildOrCurrentContext()
        {
            bool stub;
            return BuildOrCurrentContext(out stub);
        }

        public DbContext BuildOrCurrentContext(out bool createdNew)
        {
            lock (_syncRoot)
            {
                if (_context == null)
                {
                    _context = CreateContext();
                    createdNew = true;
                }
                else
                {
                    createdNew = false;
                }
                return _context;
            }
        }

        private DbContext CreateContext()
        {
            return (DbContext)ServiceProvider.GetService(typeof(DbContext));
        }
    }
}
