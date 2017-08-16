using System.Collections.Generic;

namespace QuantumLogic.Core.Authorization
{
    /// <summary>
    /// Is used as session for QuantumLogic application
    /// </summary>
    public interface IQLSession
    {
        long? UserId { get; }
        ISet<string> GrantedPermissions { get; }
    }
}
