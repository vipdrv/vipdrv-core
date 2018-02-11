using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.Logging;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Factories;
using QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers;
using System;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling
{
    /// <summary>
    /// Is used as exception handler middleware for the application's request pipeline.
    /// * Good to add as first request handler: would give have possibility to handle all exceptions on any level.
    /// </summary>
    public class ExceptionHandlerMiddleware
    {
        #region Injected dependencies

        protected readonly RequestDelegate Next;
        protected readonly ILogger Logger;
        protected readonly IExceptionHandlingTreeFactory MainExceptionHandlingTreeFactory;
        protected readonly IExceptionHandler ExceptionHandlingTree;

        #endregion

        #region Ctors

        public ExceptionHandlerMiddleware(RequestDelegate next, ILoggerFactory loggerFactory, IExceptionHandlingTreeFactory mainExceptionHandlingTreeFactory)
        {
            Next = next;
            Logger = loggerFactory.CreateLogger<ExceptionHandlerMiddleware>();
            MainExceptionHandlingTreeFactory = mainExceptionHandlingTreeFactory;
            ExceptionHandlingTree = MainExceptionHandlingTreeFactory.CreateExceptionHandlingTree();
        }

        #endregion

        public async Task Invoke(HttpContext context)
        {
            try
            {
                await Next(context);
            }
            catch (Exception ex)
            {
                await HandleExceptionAsync(context, ex);
            }
        }

        protected Task HandleExceptionAsync(HttpContext context, Exception exception)
        {
            return ExceptionHandlingTree.Handle(context, exception);
        }
    }
}
