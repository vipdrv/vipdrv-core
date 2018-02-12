using System;

namespace QuantumLogic.Core.Exceptions.Shared
{
    /// <summary>
    /// Is used to attach userfriendly message for exception
    /// </summary>
    public abstract class ExceptionWithUserfriendlyMessage : Exception
    {
        public abstract string UserfriendlyMessage { get; }

        #region Ctors

        public ExceptionWithUserfriendlyMessage()
            : base()
        { }

        public ExceptionWithUserfriendlyMessage(string message)
            : base(message)
        { }

        public ExceptionWithUserfriendlyMessage(string message, Exception innerException)
            : base(message, innerException)
        { }

        #endregion
    }
}
