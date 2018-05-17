using QuantumLogic.Core.Exceptions.Authorization;
using System;

namespace QuantumLogic.Core.Exceptions.Policy
{
    /// <summary>
    /// Is used when need to deny access for operation via policy
    /// </summary>
#warning Policy exception should not be AuthorizationException (remove this inheritance after adding support for forbitten on ftpClient side)
    public class EntityPolicyException : AuthorizationException
    {
        public string UserfriendlyMessage
        {
            get
            {
                return "Operation denied by the policy.";
            }
        }

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
