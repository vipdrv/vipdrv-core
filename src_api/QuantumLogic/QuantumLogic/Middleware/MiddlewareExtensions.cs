using Microsoft.AspNetCore.Builder;
using Microsoft.Extensions.DependencyInjection;
using QuantumLogic.WebApi.Middleware.ExceptionHandling;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Authorization;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Policy;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Request;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories.Concrete.Validation;

namespace QuantumLogic.WebApi.Middleware
{
    /// <summary>
    /// Extensions for the middleware: application's request pipeline
    /// </summary>
    public static class MiddlewareExtensions
    {
        /// <summary>
        /// Is used to add exception handler middleware to the application's request pipeline.
        /// * Good to add as first request handler: would give have possibility to handle all exceptions on any level.
        /// </summary>
        /// <param name="builder">The Microsoft.AspNetCore.Builder.IApplicationBuilder instance.</param>
        /// <returns>The Microsoft.AspNetCore.Builder.IApplicationBuilder instance.</returns>
        public static IApplicationBuilder UseExceptionHandlerMiddleware(this IApplicationBuilder applicationBuilder)
        {
            return applicationBuilder.UseMiddleware<ExceptionHandlerMiddleware>();
        }
        /// <summary>
        /// Is used to provide dependency injection for ExceptionHandlerMiddleware
        /// </summary>
        /// <param name="serviceCollection">service collection</param>
        public static IServiceCollection AddExceptionHandlerMiddlewareDependencies(this IServiceCollection serviceCollection)
        {
            // main exception handling tree factory (tree root)
            serviceCollection.AddTransient<IExceptionHandlingTreeFactory, AnyExceptionHandlingTreeFactory>();

            serviceCollection.AddTransient<AnyExceptionHandlingTreeFactory>();

            #region Authorization

            serviceCollection.AddTransient<AuthorizationExceptionHandlingTreeFactory>();

            #endregion

            #region Policy

            serviceCollection.AddTransient<PolicyExceptionHandlingTreeFactory>();

            #endregion

            #region Request

            serviceCollection.AddTransient<BadRequestExceptionHandlingTreeFactory>();
            serviceCollection.AddTransient<InvalidPatchOperationExceptionHandlingTreeFactory>();
            serviceCollection.AddTransient<InvalidRequestExceptionHandlingTreeFactory>();

            #endregion

            #region Validation

            serviceCollection.AddTransient<ValidationExceptionHandlingTreeFactory>();

            #endregion

            return serviceCollection;
        }
    }
}
