using System.Collections.Generic;
using Microsoft.AspNetCore.Http;

namespace QuantumLogic.WebApi.DataModels.Responses.Authorization
{
    public class IdentityInfo
    {
        public long UserId { get; private set; } 
        public string Username { get; private set; }
        public IList<string> GrantedRoles { get; private set; }
        public IList<string> GrantedPermissions { get; private set; }

        #region Ctors

        public IdentityInfo(long userId, string username, IList<string> grantedRoles, IList<string> grantedPermissions)
        {
            UserId = userId;
            Username = username;
            GrantedRoles = grantedRoles ?? new List<string>(0);
            GrantedPermissions = grantedPermissions ?? new List<string>(0);
        }

        #endregion
    }
}
