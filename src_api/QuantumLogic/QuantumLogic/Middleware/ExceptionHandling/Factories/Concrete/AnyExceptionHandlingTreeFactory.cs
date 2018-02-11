using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Authorization;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Policy;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Request;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Validation;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete
{
    public class AnyExceptionHandlingTreeFactory : IExceptionHandlingTreeFactory
    {
        #region Injected dependencies

        protected readonly IExceptionHandlingTreeFactory BadRequestExceptionHandlingTreeFactory;
        protected readonly IExceptionHandlingTreeFactory AuthorizationExceptionHandlingTreeFactory;
        protected readonly IExceptionHandlingTreeFactory PolicyExceptionHandlingTreeFactory;
        protected readonly IExceptionHandlingTreeFactory ValidationExceptionHandlingTreeFactory;

        #endregion

        #region Ctors

        public AnyExceptionHandlingTreeFactory()
            : this(
                  new BadRequestExceptionHandlingTreeFactory(),
                  new AuthorizationExceptionHandlingTreeFactory(),
                  new PolicyExceptionHandlingTreeFactory(),
                  new ValidationExceptionHandlingTreeFactory())
        { }

        public AnyExceptionHandlingTreeFactory(
            BadRequestExceptionHandlingTreeFactory badRequestExceptionHandlingTreeFactory,
            AuthorizationExceptionHandlingTreeFactory authorizationExceptionHandlingTreeFactory,
            PolicyExceptionHandlingTreeFactory policyExceptionHandlingTreeFactory,
            ValidationExceptionHandlingTreeFactory validationExceptionHandlingTreeFactory)
        {
            BadRequestExceptionHandlingTreeFactory = badRequestExceptionHandlingTreeFactory;
            AuthorizationExceptionHandlingTreeFactory = authorizationExceptionHandlingTreeFactory;
            PolicyExceptionHandlingTreeFactory = policyExceptionHandlingTreeFactory;
            ValidationExceptionHandlingTreeFactory = validationExceptionHandlingTreeFactory;
        }

        #endregion

        public IExceptionHandler CreateExceptionHandlingTree()
        {
            IExceptionHandler exceptionHandlingTree = new AnyExceptionHandler();

            exceptionHandlingTree.AddChild(BadRequestExceptionHandlingTreeFactory.CreateExceptionHandlingTree());
            exceptionHandlingTree.AddChild(AuthorizationExceptionHandlingTreeFactory.CreateExceptionHandlingTree());
            exceptionHandlingTree.AddChild(PolicyExceptionHandlingTreeFactory.CreateExceptionHandlingTree());
            exceptionHandlingTree.AddChild(ValidationExceptionHandlingTreeFactory.CreateExceptionHandlingTree());

            return exceptionHandlingTree;
        }
    }
}
