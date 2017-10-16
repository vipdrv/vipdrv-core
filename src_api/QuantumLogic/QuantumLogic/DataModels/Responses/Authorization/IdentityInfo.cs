using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Responses.Authorization
{
    public class IdentityInfo
    {
        public long UserId { get; private set; } 
        public string Username { get; private set; }
        public string AvatarUrl { get; private set; }
        public IList<string> GrantedRoles { get; private set; }
        public IList<string> GrantedPermissions { get; private set; }

        #region Ctors

        public IdentityInfo(long userId, string username, IList<string> grantedRoles, IList<string> grantedPermissions, string avatarUrl = null)
        {
            UserId = userId;
            Username = username;
            GrantedRoles = grantedRoles ?? new List<string>(0);
            GrantedPermissions = grantedPermissions ?? new List<string>(0);
            AvatarUrl = avatarUrl;
        }

        #endregion
    }
}
