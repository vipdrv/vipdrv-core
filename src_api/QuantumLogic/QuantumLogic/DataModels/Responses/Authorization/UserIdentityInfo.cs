using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Responses.Authorization
{
    public class UserIdentityInfo
    {
        public long UserId { get; private set; } 
        public string Username { get; private set; }
        public string Avatar { get; private set; }
        public IList<string> GrantedRoles { get; private set; }
        public IList<string> GrantedPermissions { get; private set; }

        #region Ctors

        public UserIdentityInfo(long userId, string username, IList<string> grantedRoles, IList<string> grantedPermissions, string avatar = null)
        {
            UserId = userId;
            Username = username;
            GrantedRoles = grantedRoles ?? new List<string>(0);
            GrantedPermissions = grantedPermissions ?? new List<string>(0);
            Avatar = avatar;
        }

        #endregion
    }
}
