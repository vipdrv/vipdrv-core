using System;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Responses.Authorization
{
    public class TokenResponse : IdentityInfo
    {
        public string Issuer { get; private set; }
        public IList<string> Audiences { get; private set; }
        public string Token { get; private set; }
        public string TokenType { get; set; }
        public string Sub { get; private set; }
        public DateTime ExpireDateTimeUtc { get; private set; }

        #region Ctors

        public TokenResponse(
            string issuer,
            IList<string> audiences,
            string token,
            string tokenType,
            string sub,
            DateTime expireDateTimeUtc,
            IdentityInfo userIdentityInfo)
            : this(
                  issuer, 
                  audiences, 
                  token, 
                  tokenType, 
                  sub, 
                  expireDateTimeUtc, 
                  userIdentityInfo.UserId, 
                  userIdentityInfo.Username,
                  userIdentityInfo.GrantedRoles, 
                  userIdentityInfo.GrantedPermissions)
        { }

        public TokenResponse(
            string issuer, 
            IList<string> audiences, 
            string token, 
            string tokenType, 
            string sub,
            DateTime expireDateTimeUtc,
            int userId,
            string username,
            IList<string> grantedRoles,
            IList<string> grantedPermissions)
            : base(userId, username, grantedRoles, grantedPermissions)
        {
            Issuer = issuer;
            Audiences = audiences ?? new List<string>(0);
            Token = token;
            Sub = sub;
            ExpireDateTimeUtc = expireDateTimeUtc;
            TokenType = tokenType;
        }

        #endregion
    }
}
