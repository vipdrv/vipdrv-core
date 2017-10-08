using System;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Responses.Authorization
{
    public class TokenResponse
    {
        public string Issuer { get; private set; }
        public IList<string> Audiences { get; private set; }
        public string Token { get; private set; }
        public string Sid { get; private set; }
        public string Username { get; private set; }
        public DateTime ExpireDateTimeUtc { get; private set; }

        #region Ctors

        public TokenResponse(string issuer, IList<string> audiences, string token, string sid, string username, DateTime expireDateTimeUtc)
        {
            Issuer = issuer;
            Audiences = audiences ?? new List<string>(0);
            Token = token;
            Sid = sid;
            Username = username;
            ExpireDateTimeUtc = expireDateTimeUtc;
        }

        #endregion
    }
}
