using QuantumLogic.Core.Authorization;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.Authorization
{
    public class QLSession : IQLSession
    {
        public long? UserId { get; private set; }

        public ISet<string> GrantedPermissions { get; private set; }

        #region Ctors

        public QLSession()
            : this(new HashSet<string>())
        { }

        public QLSession(ISet<string> grantedPermissions)
        {
            GrantedPermissions = grantedPermissions;
        }

        public QLSession(long userId, ISet<string> grantedPermissions)
            : this(grantedPermissions)
        {
            UserId = userId;
        }

        #endregion
    }
}
