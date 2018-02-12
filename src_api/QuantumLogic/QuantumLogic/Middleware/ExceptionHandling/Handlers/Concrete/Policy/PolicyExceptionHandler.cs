using Microsoft.AspNetCore.Http;
using QuantumLogic.Core.Exceptions.Policy;
using QuantumLogic.WebApi.DataModels.Dtos.Shared.Error;
using System;
using System.Net;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.Policy
{
    public class PolicyExceptionHandler : AbstractExceptionHandler<EntityPolicyException>
    {
        public override int StatusCode
        {
            get
            {
                return (int)HttpStatusCode.Forbidden;
            }
        }

        public override bool CanHandle(HttpContext context, Exception ex)
        {
            return ex is EntityPolicyException;
        }

        protected override ErrorDto CreateErrorDto(HttpContext context, EntityPolicyException ex)
        {
            return new ErrorDto(context.TraceIdentifier, ex.UserfriendlyMessage);
        }
    }
}
