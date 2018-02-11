using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.Policy;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Policy
{
    public class PolicyExceptionHandlingTreeFactory : IExceptionHandlingTreeFactory
    {
        #region Ctors

        public PolicyExceptionHandlingTreeFactory()
        { }

        #endregion

        public IExceptionHandler CreateExceptionHandlingTree()
        {
            IExceptionHandler exceptionHandlingTree = new PolicyExceptionHandler();
            return exceptionHandlingTree;
        }
    }
}
