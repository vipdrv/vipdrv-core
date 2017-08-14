using System;

namespace QuantumLogic.Core.Exceptions.Validation
{
    /// <summary>
    /// Is used when need to deny operation (cuz entity properties are invalid) via validation
    /// </summary>
    public class ValidateEntityPropertiesException : ValidationException
    {
        #region Ctors

        public ValidateEntityPropertiesException()
            : base()
        { }

        public ValidateEntityPropertiesException(string message)
            : base(message)
        { }

        public ValidateEntityPropertiesException(string message, Exception innerException)
            : base(message, innerException)
        { }

        #endregion
    }
}
