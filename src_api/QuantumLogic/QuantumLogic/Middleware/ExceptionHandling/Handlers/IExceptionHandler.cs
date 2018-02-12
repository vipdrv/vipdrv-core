using Microsoft.AspNetCore.Http;
using System;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers
{
    public interface IExceptionHandler
    {
        Task Handle(HttpContext context, Exception exception);
        bool CanHandle(HttpContext context, Exception ex);
        void AddChild(IExceptionHandler child);
    }
}
