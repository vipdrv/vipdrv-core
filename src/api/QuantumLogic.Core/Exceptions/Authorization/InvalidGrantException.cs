using System;

namespace QuantumLogic.Core.Exceptions.Authorization
{
    /// <summary>
    /// Should be throwed as exception when authorization grant type is not valid
    /// </summary>
    public class InvalidGrantException : AuthorizationException
    {
        #region Ctors

        public InvalidGrantException()
            : base()
        { }

        public InvalidGrantException(string message)
            : base(message)
        { }

        public InvalidGrantException(string message, Exception innerException)
            : base(message, innerException)
        { }

        #endregion
    }
}
