using Microsoft.AspNetCore.Http;
using QuantumLogic.WebApi.DataModels.Dtos.Shared.Error;
using QuantumLogic.WebApi.Exceptions;
using System;
using System.Linq;
using System.Net;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.Request
{
    public class InvalidRequestExceptionHandler : AbstractExceptionHandler<InvalidRequestException>
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
            return ex is InvalidRequestException;
        }

        protected override ErrorDto CreateErrorDto(HttpContext context, InvalidRequestException ex)
        {
            return new ErrorWithFailuresInfoDto<ValidationFailureDto>(context.TraceIdentifier, ex.UserfriendlyMessage, ex.ValidationFailures.ToList());
        }
    }
}
