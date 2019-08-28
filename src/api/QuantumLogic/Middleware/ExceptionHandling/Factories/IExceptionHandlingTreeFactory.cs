using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories
{
    public interface IExceptionHandlingTreeFactory
    {
        IExceptionHandler CreateExceptionHandlingTree();
    }
}
