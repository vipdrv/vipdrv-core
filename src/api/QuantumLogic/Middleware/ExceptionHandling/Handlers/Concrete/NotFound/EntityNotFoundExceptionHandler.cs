using Microsoft.AspNetCore.Http;
using QuantumLogic.Core.Exceptions.NotFound;
using QuantumLogic.WebApi.DataModels.Dtos.Shared.Error;
using System;
using System.Net;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers.Concrete.NotFound
{
    public class EntityNotFoundExceptionHandler : AbstractExceptionHandler<EntityNotFoundException>
    {
        public override int StatusCode
        {
            get
            {
                return (int)HttpStatusCode.NotFound;
            }
        }

        public override bool CanHandle(HttpContext context, Exception ex)
        {
            return ex is EntityNotFoundException;
        }

        protected override ErrorDto CreateErrorDto(HttpContext context, EntityNotFoundException ex)
        {
            return new ErrorDto(context.TraceIdentifier, $"Entity not found! {ex.Message}");
        }
    }
}
