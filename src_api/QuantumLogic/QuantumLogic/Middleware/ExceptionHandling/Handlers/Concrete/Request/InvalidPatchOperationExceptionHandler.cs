using Microsoft.AspNetCore.Http;
using QuantumLogic.WebApi.DataModels.Dtos.Shared.Error;
using QuantumLogic.WebApi.Exceptions;
using System;
using System.Linq;
using System.Net;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.Request
{
    public class InvalidPatchOperationExceptionHandler : AbstractExceptionHandler<InvalidPatchOperationException>
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
            return ex is InvalidPatchOperationException;
        }

        protected override ErrorDto CreateErrorDto(HttpContext context, InvalidPatchOperationException ex)
        {
            return new ErrorWithFailuresInfoDto<PatchFailureDto>(context.TraceIdentifier, ex.UserfriendlyMessage, ex.PatchFailures.ToList());
        }
    }
}
