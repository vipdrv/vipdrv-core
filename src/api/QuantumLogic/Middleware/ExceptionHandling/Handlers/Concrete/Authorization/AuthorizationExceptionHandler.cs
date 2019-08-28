using Microsoft.AspNetCore.Http;
using QuantumLogic.Core.Exceptions.Authorization;
using QuantumLogic.WebApi.DataModels.Dtos.Shared.Error;
using System;
using System.Net;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.Authorization
{
    public class AuthorizationExceptionHandler : AbstractExceptionHandler<AuthorizationException>
    {
        public override int StatusCode
        {
            get
            {
                return (int)HttpStatusCode.Unauthorized;
            }
        }

        public override bool CanHandle(HttpContext context, Exception ex)
        {
            return ex is AuthorizationException;
        }

        protected override ErrorDto CreateErrorDto(HttpContext context, AuthorizationException ex)
        {
            return new ErrorDto(context.TraceIdentifier, "Authorization failed!");
        }
    }
}
