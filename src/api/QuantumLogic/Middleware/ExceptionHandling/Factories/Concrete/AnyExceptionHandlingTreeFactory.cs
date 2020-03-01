using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Authorization;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.NotFound;
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
        protected readonly IExceptionHandlingTreeFactory EntityNotFoundExceptionHandlingTreeFactory;

        #endregion

        #region Ctors

        public AnyExceptionHandlingTreeFactory()
            : this(
                  new BadRequestExceptionHandlingTreeFactory(),
                  new AuthorizationExceptionHandlingTreeFactory(),
                  new PolicyExceptionHandlingTreeFactory(),
                  new ValidationExceptionHandlingTreeFactory(),
                  new EntityNotFoundExceptionHandlingTreeFactory())
        { }

        public AnyExceptionHandlingTreeFactory(
            BadRequestExceptionHandlingTreeFactory badRequestExceptionHandlingTreeFactory,
            AuthorizationExceptionHandlingTreeFactory authorizationExceptionHandlingTreeFactory,
            PolicyExceptionHandlingTreeFactory policyExceptionHandlingTreeFactory,
            ValidationExceptionHandlingTreeFactory validationExceptionHandlingTreeFactory,
            EntityNotFoundExceptionHandlingTreeFactory entityNotFoundExceptionHandlingTreeFactory)
        {
            BadRequestExceptionHandlingTreeFactory = badRequestExceptionHandlingTreeFactory;
            AuthorizationExceptionHandlingTreeFactory = authorizationExceptionHandlingTreeFactory;
            PolicyExceptionHandlingTreeFactory = policyExceptionHandlingTreeFactory;
            ValidationExceptionHandlingTreeFactory = validationExceptionHandlingTreeFactory;
            EntityNotFoundExceptionHandlingTreeFactory = entityNotFoundExceptionHandlingTreeFactory;
        }

        #endregion

        public IExceptionHandler CreateExceptionHandlingTree()
        {
            IExceptionHandler exceptionHandlingTree = new AnyExceptionHandler();

            exceptionHandlingTree.AddChild(BadRequestExceptionHandlingTreeFactory.CreateExceptionHandlingTree());
            exceptionHandlingTree.AddChild(AuthorizationExceptionHandlingTreeFactory.CreateExceptionHandlingTree());
            exceptionHandlingTree.AddChild(PolicyExceptionHandlingTreeFactory.CreateExceptionHandlingTree());
            exceptionHandlingTree.AddChild(ValidationExceptionHandlingTreeFactory.CreateExceptionHandlingTree());
            exceptionHandlingTree.AddChild(EntityNotFoundExceptionHandlingTreeFactory.CreateExceptionHandlingTree());

            return exceptionHandlingTree;
        }
    }
}
