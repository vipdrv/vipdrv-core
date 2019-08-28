using System;

namespace QuantumLogic.Core.Exceptions.NotSupported
{
    /// <summary>
    /// Should be throwed as exception when operation is not supported
    /// </summary>
    public class OperationIsNotSupportedException : NotSupportedException
    {
        #region Ctors

        public OperationIsNotSupportedException()
            : base()
        { }

        public OperationIsNotSupportedException(string message)
            : base(message)
        { }

        public OperationIsNotSupportedException(string message, Exception innerException)
            : base(message, innerException)
        { }

        #endregion
    }
}
