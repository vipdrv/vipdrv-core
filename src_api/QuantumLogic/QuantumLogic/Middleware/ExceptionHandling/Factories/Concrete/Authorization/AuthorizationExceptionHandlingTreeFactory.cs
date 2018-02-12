using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.Authorization;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Authorization
{
    public class AuthorizationExceptionHandlingTreeFactory : IExceptionHandlingTreeFactory
    {
        #region Ctors

        public AuthorizationExceptionHandlingTreeFactory()
        { }

        #endregion

        public IExceptionHandler CreateExceptionHandlingTree()
        {
            IExceptionHandler exceptionHandlingTree = new AuthorizationExceptionHandler();
            return exceptionHandlingTree;
        }
    }
}
