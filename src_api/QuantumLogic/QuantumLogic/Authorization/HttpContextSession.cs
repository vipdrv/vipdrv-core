using System;
using QuantumLogic.Core.Authorization;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Http;
using QuantumLogic.WebApi.Controllers.Authorization;
using QuantumLogic.WebApi.DataModels.Responses.Authorization;

namespace QuantumLogic.WebApi.Authorization
{
    /// <summary>
    /// Session was constructed via Http context
    /// </summary>
    public class HttpContextSession : IQLSession
    {
        public long? UserId { get; set; }

        public string Username { get; private set; }

        public ISet<string> GrantedPermissions { get; private set; }

        #region Ctors

        public HttpContextSession(IHttpContextAccessor contextAccessor)
        {
            GetCurrecntUserGrantedPermissions(contextAccessor.HttpContext.User.Claims.ToDictionary((item) => item.Type, (item) => item.Value)).Wait();
        }

        #endregion

        protected async Task GetCurrecntUserGrantedPermissions(IDictionary<string, string> userClaims)
        {
            IdentityInfo identityInfo;
            
            try
            {
                identityInfo = (await AuthorizationController.ParseIdentityInfoFromIdentityClaimsAsync(userClaims));
            }
            catch (ArgumentException)
            {
                identityInfo = null;
            }

            if (identityInfo != null)
            {
                UserId = identityInfo.UserId;
                Username = identityInfo.Username;
                GrantedPermissions = new HashSet<string>(identityInfo.GrantedPermissions);
            }
            else
            {
                UserId = null;
                Username = null;
                GrantedPermissions = new HashSet<string>();
            }
        }
    }
}
