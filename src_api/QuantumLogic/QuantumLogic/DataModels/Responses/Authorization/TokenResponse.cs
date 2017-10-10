using System;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Responses.Authorization
{
    public class TokenResponse : UserIdentityInfo
    {
        public string Issuer { get; private set; }
        public IList<string> Audiences { get; private set; }
        public string Token { get; private set; }
        public string Sub { get; private set; }
        public DateTime ExpireDateTimeUtc { get; private set; }

        #region Ctors

        public TokenResponse(string issuer, IList<string> audiences, string token, string sub, DateTime expireDateTimeUtc, UserIdentityInfo userIdentityInfo)
            : this(
                  issuer, audiences, token, sub, expireDateTimeUtc, userIdentityInfo.UserId, userIdentityInfo.Username,
                  userIdentityInfo.GrantedRoles, userIdentityInfo.GrantedPermissions, userIdentityInfo.AvatarUrl)
        { }

        public TokenResponse(
            string issuer, IList<string> audiences, string token, string sub, DateTime expireDateTimeUtc, 
            long userId, string username, IList<string> grantedRoles, IList<string> grantedPermissions, string avatarUrl = null)
            : base(userId, username, grantedRoles, grantedPermissions, avatarUrl)
        {
            Issuer = issuer;
            Audiences = audiences ?? new List<string>(0);
            Token = token;
            Sub = sub;
            ExpireDateTimeUtc = expireDateTimeUtc;
        }

        #endregion
    }
}
