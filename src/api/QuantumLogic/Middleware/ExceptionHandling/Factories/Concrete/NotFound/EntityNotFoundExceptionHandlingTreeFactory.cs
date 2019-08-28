using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.NotFound;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.NotFound
{
    public class EntityNotFoundExceptionHandlingTreeFactory : IExceptionHandlingTreeFactory
    {
        public IExceptionHandler CreateExceptionHandlingTree()
        {
            IExceptionHandler exceptionHandlingTree = new EntityNotFoundExceptionHandler();
            return exceptionHandlingTree;
        }
    }
}
