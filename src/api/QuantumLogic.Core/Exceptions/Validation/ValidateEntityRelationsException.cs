using System;

namespace QuantumLogic.Core.Exceptions.Validation
{
    /// <summary>
    /// Is used when need to deny operation (cuz entity relations are invalid) via validation
    /// </summary>
    public class ValidateEntityRelationsException : ValidationException
    {
        #region Ctors

        public ValidateEntityRelationsException()
            : base()
        { }

        public ValidateEntityRelationsException(string message)
            : base(message)
        { }

        public ValidateEntityRelationsException(string message, Exception innerException)
            : base(message, innerException)
        { }

        #endregion
    }
}
