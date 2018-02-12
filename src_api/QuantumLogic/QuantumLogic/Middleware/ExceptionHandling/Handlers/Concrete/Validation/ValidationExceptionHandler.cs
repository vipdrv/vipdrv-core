using Microsoft.AspNetCore.Http;
using QuantumLogic.Core.Exceptions.Validation;
using QuantumLogic.WebApi.Constants;
using QuantumLogic.WebApi.DataModels.Dtos.Shared.Error;
using System;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.Validation
{
    public class ValidationExceptionHandler : AbstractExceptionHandler<ValidationException>
    {
        public override int StatusCode
        {
            get
            {
                return (int)CustomHttpCode.UnprocessableEntity;
            }
        }

        public override bool CanHandle(HttpContext context, Exception ex)
        {
            return ex is ValidationException;
        }

        protected override ErrorDto CreateErrorDto(HttpContext context, ValidationException ex)
        {
            return new ErrorDto(context.TraceIdentifier, ex.UserfriendlyMessage);
        }
    }
}
