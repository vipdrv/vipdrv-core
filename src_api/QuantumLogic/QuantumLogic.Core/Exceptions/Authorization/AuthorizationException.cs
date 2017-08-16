using System;

namespace QuantumLogic.Core.Exceptions.Authorization
{
    /// <summary>
    /// Should be throwed as exception when authorization failed
    /// </summary>
    public class AuthorizationException : Exception
    {
        #region Ctors

        public AuthorizationException()
            : base()
        { }

        public AuthorizationException(string message)
            : base(message)
        { }

        public AuthorizationException(string message, Exception innerException)
            : base(message, innerException)
        { }

        #endregion
    }
}
