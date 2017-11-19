using System;

namespace QuantumLogic.Core.Exceptions.Authorization
{
    public class PasswordIsNotValidException : AuthorizationException
    {
        #region Ctors

        public PasswordIsNotValidException()
            : base()
        { }

        public PasswordIsNotValidException(string message)
            : base(message)
        { }

        public PasswordIsNotValidException(string message, Exception innerException)
            : base(message, innerException)
        { }

        #endregion
    }
}
