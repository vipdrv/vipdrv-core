using System;

namespace QuantumLogic.Core.Exceptions.NotFound
{
    public class EntityNotFoundException : Exception
    {
        #region Ctors

        public EntityNotFoundException()
            : base()
        { }

        public EntityNotFoundException(string message)
            : base(message)
        { }

        public EntityNotFoundException(string message, Exception innerException)
            : base(message, innerException)
        { }

        #endregion
    }
}
