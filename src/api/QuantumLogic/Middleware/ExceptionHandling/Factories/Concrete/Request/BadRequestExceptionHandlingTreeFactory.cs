using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.Request;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Request
{
    public class BadRequestExceptionHandlingTreeFactory : IExceptionHandlingTreeFactory
    {
        #region Injected dependencies

        protected readonly IExceptionHandlingTreeFactory InvalidRequestExceptionHandlingTreeFactory;
        protected readonly IExceptionHandlingTreeFactory InvalidPatchOperationExceptionHandlingTreeFactory;

        #endregion

        #region Ctors

        public BadRequestExceptionHandlingTreeFactory()
            : this(
                  new InvalidRequestExceptionHandlingTreeFactory(),
                  new InvalidPatchOperationExceptionHandlingTreeFactory())
        { }

        public BadRequestExceptionHandlingTreeFactory(
            InvalidRequestExceptionHandlingTreeFactory invalidRequestExceptionHandlingTreeFactory,
            InvalidPatchOperationExceptionHandlingTreeFactory invalidPatchOperationExceptionHandlingTreeFactory)
        {
            InvalidRequestExceptionHandlingTreeFactory = invalidRequestExceptionHandlingTreeFactory;
            InvalidPatchOperationExceptionHandlingTreeFactory = invalidPatchOperationExceptionHandlingTreeFactory;
        }

        #endregion

        public IExceptionHandler CreateExceptionHandlingTree()
        {
            IExceptionHandler exceptionHandlingTree = new BadRequestExceptionHandler();
            exceptionHandlingTree.AddChild(InvalidRequestExceptionHandlingTreeFactory.CreateExceptionHandlingTree());
            exceptionHandlingTree.AddChild(InvalidPatchOperationExceptionHandlingTreeFactory.CreateExceptionHandlingTree());
            return exceptionHandlingTree;
        }
    }
}
