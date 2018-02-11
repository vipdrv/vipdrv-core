using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.Request;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Request
{
    public class InvalidPatchOperationExceptionHandlingTreeFactory : IExceptionHandlingTreeFactory
    {
        public IExceptionHandler CreateExceptionHandlingTree()
        {
            return new InvalidPatchOperationExceptionHandler();
        }
    }
}
