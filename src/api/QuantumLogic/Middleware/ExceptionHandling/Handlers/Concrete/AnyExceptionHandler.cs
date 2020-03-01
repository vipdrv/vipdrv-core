using Microsoft.AspNetCore.Http;
using QuantumLogic.WebApi.DataModels.Dtos.Shared.Error;
using System;
using System.Net;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete
{
    public class AnyExceptionHandler : AbstractExceptionHandler<Exception>
    {
        public override int StatusCode
        {
            get
            {
                return (int)HttpStatusCode.InternalServerError;
            }
        }

        public override bool CanHandle(HttpContext context, Exception ex)
        {
            return true;
        }

        protected override ErrorDto CreateErrorDto(HttpContext context, Exception ex)
        {
            return new ErrorDto(context.TraceIdentifier, "Internal server error.");
        }
    }
}
