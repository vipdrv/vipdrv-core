using QuantumLogic.Core.Exceptions.Shared;
using System;

namespace QuantumLogic.WebApi.Exceptions
{
    /// <summary>
    /// Is used as exception when request is not successfully parsed as expected type
    /// </summary>
    public class BadRequestException : ExceptionWithUserfriendlyMessage
    {
        public override string UserfriendlyMessage
        {
            get
            {
                return "Bad request: request is not successfully parsed as expected type.";
            }
        }

        #region Ctors

        public BadRequestException()
            : base()
        { }

        public BadRequestException(string message)
            : base(message)
        { }

        public BadRequestException(string message, Exception innerException)
            : base(message, innerException)
        { }

        #endregion
    }
}
