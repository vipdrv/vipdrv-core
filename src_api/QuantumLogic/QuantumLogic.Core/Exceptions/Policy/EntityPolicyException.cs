using QuantumLogic.Core.Exceptions.Authorization;
using System;

namespace QuantumLogic.Core.Exceptions.Policy
{
    /// <summary>
    /// Is used when need to deny access for operation via policy
    /// </summary>
    public class EntityPolicyException : AuthorizationException
    {
        #region Ctors

        public EntityPolicyException()
            : base()
        { }

        public EntityPolicyException(string message)
            : base(message)
        { }

        public EntityPolicyException(string message, Exception innerException)
            : base(message, innerException)
        { }

        #endregion
    }
}
