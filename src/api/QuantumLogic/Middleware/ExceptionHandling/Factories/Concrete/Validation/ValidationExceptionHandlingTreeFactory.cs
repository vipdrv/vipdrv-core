using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.Validation;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Validation
{
    public class ValidationExceptionHandlingTreeFactory : IExceptionHandlingTreeFactory
    {
        #region Ctors

        public ValidationExceptionHandlingTreeFactory()
        { }

        #endregion

        public IExceptionHandler CreateExceptionHandlingTree()
        {
            IExceptionHandler exceptionHandlingTree = new ValidationExceptionHandler();
            return exceptionHandlingTree;
        }
    }
}
