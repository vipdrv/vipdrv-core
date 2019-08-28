using Microsoft.AspNetCore.Http;
using Newtonsoft.Json;
using QuantumLogic.WebApi.DataModels.Dtos.Shared.Error;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Middleware.ExceptionHandling.Handlers
{
    public abstract class AbstractExceptionHandler<TException> : IExceptionHandler
       where TException : Exception
    {
        public const string DefaultErrorContentType = "application/json";
        public abstract int StatusCode { get; }
        protected IList<IExceptionHandler> Children { get; set; }

        #region Ctors

        public AbstractExceptionHandler()
        {
            Children = new List<IExceptionHandler>();
        }

        #endregion

        public Task Handle(HttpContext context, Exception exception)
        {
            IEnumerable<IExceptionHandler> canHandleChildren = Children
                .Where(r => r.CanHandle(context, exception));
            if (canHandleChildren.Count() > 0)
            {
                return canHandleChildren.First().Handle(context, exception);
            }
            else
            {
                context.Response.StatusCode = StatusCode;
                context.Response.ContentType = GetResponseContentType(context);
                string result = SerializeViaContentType(
                    context.Response.ContentType,
                    CreateErrorDto(context, exception as TException));
                return context.Response.WriteAsync(result);
            }
        }
        public virtual bool CanHandle(HttpContext context, Exception ex)
        {
            return ex is TException;
        }
        public void AddChild(IExceptionHandler child)
        {
            Children.Add(child);
        }

        #region Helpers

        protected abstract ErrorDto CreateErrorDto(HttpContext context, TException ex);

        private string SerializeViaContentType(string contentType, ErrorDto error)
        {
            if (contentType == "application/json")
            {
                return JsonConvert.SerializeObject(error);
            }
            throw new ArgumentException(nameof(contentType));
        }
        private string GetResponseContentType(HttpContext context)
        {
            return DefaultErrorContentType;
        }

        #endregion
    }
}
