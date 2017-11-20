using System.Collections.Generic;

namespace QuantumLogic.Core.Domain.Services.Main.Users.Models
{
    public class UserInfo
    {
        public string Sub { get; private set; }
        public int UserId { get; private set; }
        public string Username { get; private set; }
        public string AvatarUrl { get; private set; }
        public IList<string> GrantedRoles { get; private set; }
        public IList<string> GrantedPermissions { get; private set; }

        #region Ctors

        public UserInfo(string sub, int userId, string username, IList<string> grantedRoles, IList<string> grantedPermissions, string avatarUrl)
        {
            Sub = sub;
            UserId = userId;
            Username = username;
            GrantedRoles = grantedRoles;
            GrantedPermissions = grantedPermissions;
            AvatarUrl = avatarUrl;
        }

        #endregion
    }
}
