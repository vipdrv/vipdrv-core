using System;

namespace QuantumLogic.Core.Exceptions.Validation
{
    /// <summary>
    /// Is used when need to deny operation via validation
    /// </summary>
    public class ValidationException : Exception
    {
        #region Ctors

        public ValidationException()
            : base()
        { }

        public ValidationException(string message)
            : base(message)
        { }

        public ValidationException(string message, Exception innerException)
            : base(message, innerException)
        { }

        #endregion
    }
}
