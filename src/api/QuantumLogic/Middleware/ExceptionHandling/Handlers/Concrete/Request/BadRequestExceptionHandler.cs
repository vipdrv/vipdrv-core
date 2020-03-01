using Microsoft.AspNetCore.Http;
using QuantumLogic.WebApi.DataModels.Dtos.Shared.Error;
using QuantumLogic.WebApi.Exceptions;
using System;
using System.Net;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.Request
{
    public class BadRequestExceptionHandler : AbstractExceptionHandler<BadRequestException>
    {
        public override int StatusCode
        {
            get
            {
                return (int)HttpStatusCode.BadRequest;
            }
        }

        public override bool CanHandle(HttpContext context, Exception ex)
        {
            return ex is BadRequestException;
        }

        protected override ErrorDto CreateErrorDto(HttpContext context, BadRequestException ex)
        {
            return new ErrorDto(context.TraceIdentifier, ex.UserfriendlyMessage);
        }
    }
}
