using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Services.Main.Users;
using QuantumLogic.Core.Domain.Services.Main.Users.Models;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.Core.Exceptions.Authorization;
using QuantumLogic.WebApi.Authorization.Options;
using QuantumLogic.WebApi.DataModels.Requests.Authorization;
using QuantumLogic.WebApi.DataModels.Responses.Authorization;
using System;
using System.Collections.Generic;
using System.IdentityModel.Tokens.Jwt;
using System.Linq;
using System.Net;
using System.Security.Claims;
using System.Security.Principal;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Authorization
{
    [Route("/")]
    public class AuthorizationController : Controller
    {
        #region Constants

        protected const string TokenType = "bearer";

        protected const string IdentityName = "UserIdentity";
        protected const string UserIdClaimKey = "userId";
        protected const string UsernameClaimKey = "username";
        protected const string GrantedRolesClaimKey = "grantedRoles";
        protected const string GrantedPermissionsClaimKey = "grantedPermissions";

        protected const string GrantTypeUsername = "username";
        protected const string GrantTypeEmail = "email";
        protected const string GrantTypePhoneNumber = "phonenumber";

        #endregion

        #region Injected Dependencies

        public JwtSecurityTokenHandler JwtTokenHandler { get; private set; }
        public IQLUnitOfWorkManager UowManager { get; private set; }
        public IUserDomainService UserDomainService { get; private set; }

        #endregion

        #region Ctors

        public AuthorizationController(IQLUnitOfWorkManager uowManager, JwtSecurityTokenHandler tokenHandler, IUserDomainService userDomainService)
            : base()
        {
            JwtTokenHandler = tokenHandler;
            UowManager = uowManager;
            UserDomainService = userDomainService;
        }

        #endregion

        [HttpPost("token")]
        public async Task<TokenResponse> CreateJwtTokenAsync([FromBody]TokenRequest request)
        {
            if (request == null)
            {
                throw new ArgumentException(nameof(TokenRequest));
            }
            TokenResponse response;
            try
            {
                DateTime expireDateTimeUtc = DateTime.UtcNow.AddMilliseconds(QLAuthenticationOptions.TokenLifetimeMS);
                ClaimsIdentityBox identityBox = await GetUserIdentityAsync(request.Login, request.Password, request.GrantType);
                if (identityBox != null)
                {
                    JwtSecurityToken token = JwtTokenHandler
                        .CreateJwtSecurityToken(
                            subject: identityBox.ClaimsIdentity,
                            signingCredentials: QLAuthenticationOptions.GetSigningCredentials(),
                            audience: QLAuthenticationOptions.Audience,
                            issuer: QLAuthenticationOptions.Issuer,
                            expires: expireDateTimeUtc);
                    response = new TokenResponse(
                        token.Issuer, token.Audiences.ToList(), JwtTokenHandler.WriteToken(token), TokenType, identityBox.Sub, expireDateTimeUtc,
                        await ParseIdentityInfoFromIdentityClaimsAsync(identityBox.ClaimsIdentity.Claims.ToDictionary((item) => item.Type, (item) => item.Value)));
                }
                else
                {
                    throw new AuthorizationException("Login or password is incorrect.");
                }
            }
            catch (AuthorizationException)
            {
                Response.StatusCode = (int)HttpStatusCode.Unauthorized;
                response = null;
            }
            return response;
        }

        [Authorize]
        [HttpGet("identity-info")]
        public Task<IdentityInfo> GetIdentityInfoAsync()
        {
            return ParseIdentityInfoFromIdentityClaimsAsync(HttpContext.User.Claims.ToDictionary((item) => item.Type, (item) => item.Value));
        }

        public static Task<IdentityInfo> ParseIdentityInfoFromIdentityClaimsAsync(IDictionary<string, string> identityClaims)
        {
            string grantedRolesValue;
            if (!identityClaims.TryGetValue(GrantedRolesClaimKey, out grantedRolesValue))
            {
                grantedRolesValue = null;
            }
            string grantedPermissionsValue;
            if (!identityClaims.TryGetValue(GrantedPermissionsClaimKey, out grantedPermissionsValue))
            {
                grantedPermissionsValue = null;
            }
            IdentityInfo result = new IdentityInfo(
                Int64.Parse(identityClaims[UserIdClaimKey]),
                identityClaims[UsernameClaimKey],
                String.IsNullOrEmpty(grantedRolesValue) ? new string[0] : grantedRolesValue.Split(','),
                String.IsNullOrEmpty(grantedPermissionsValue) ? new string[0] : grantedPermissionsValue.Split(','));
            return Task.FromResult(result);
        }

        #region Helpers

        private async Task<ClaimsIdentityBox> GetUserIdentityAsync(string login, string password, string grantType)
        {
            ClaimsIdentityBox claimsIdentityBox;
            UserInfo userInfo = await UserDomainService.GetUserInfoAsync(login, password, GetLoginComparer(grantType));
            if (userInfo != null)
            {
                ClaimsIdentity identity = new ClaimsIdentity(
                    new GenericIdentity(IdentityName),
                    new[] 
                    {
                        new Claim(UserIdClaimKey, userInfo.UserId.ToString()),
                        new Claim(UsernameClaimKey, userInfo.Username),
                        new Claim(GrantedRolesClaimKey, String.Join(",", userInfo.GrantedRoles)),
                        new Claim(GrantedPermissionsClaimKey, String.Join(",", userInfo.GrantedPermissions))
                    });
                claimsIdentityBox = new ClaimsIdentityBox(userInfo.Sub, userInfo.Username, identity);
            }
            else
            {
                claimsIdentityBox = null;
            }
            return claimsIdentityBox;
        }

        private Func<Core.Domain.Entities.MainModule.User, string, bool> GetLoginComparer(string grantType)
        {
            Func<Core.Domain.Entities.MainModule.User, string, bool> loginComparer;
            if (String.Equals(grantType, GrantTypeUsername, StringComparison.OrdinalIgnoreCase))
            {
                loginComparer = (user, login) => user.Username == login;
            }
            else if (String.Equals(grantType, GrantTypeEmail, StringComparison.OrdinalIgnoreCase))
            {
                loginComparer = (user, login) => user.Email == login;
            }
            else if (String.Equals(grantType, GrantTypePhoneNumber, StringComparison.OrdinalIgnoreCase))
            {
                loginComparer = (user, login) => user.PhoneNumber == login;
            }
            else
            {
                throw new InvalidGrantException($"Grant type ({grantType}) is not valid.");
            }
            return loginComparer;
        }

        /// <summary>
        /// Is used like box with info about claim identity
        /// </summary>
        private class ClaimsIdentityBox
        {
            public string Sub { get; private set; }
            public string Username { get; private set; }
            public ClaimsIdentity ClaimsIdentity { get; private set; }

            #region Ctors

            public ClaimsIdentityBox(string sub, string username, ClaimsIdentity claimsIdentity)
            {
                Sub = sub;
                Username = username;
                ClaimsIdentity = claimsIdentity;
            }

            #endregion
        }

        #endregion
    }
}
