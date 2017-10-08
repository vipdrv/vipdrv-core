using Microsoft.IdentityModel.Tokens;
using System;
using System.Text;

namespace QuantumLogic.WebApi.Authorization.Options
{
    public static class QLAuthenticationOptions
    {
        #region Settings

        /// <summary>
        /// Is used like security key for all application tokens (changing this key cause to make all old tokens invalid)
        /// </summary>
        private const string Key = "test-drive-security-key";

        public const string Issuer = "TestDriveAuthServer";
        public const string Audience = "TestDriveAdminPanel";
        public const int TokenLifetimeMS = 7 * 24 * 60 * 60 * 1000; 
        public const int ClockSkewMS = 0;

        #endregion

        public static SymmetricSecurityKey GetSymmetricSecurityKey()
        {
            return new SymmetricSecurityKey(Encoding.ASCII.GetBytes(Key));
        }

        public static TokenValidationParameters GetTokenValidationParameters()
        {
            return new TokenValidationParameters
            {
                ValidIssuer = Issuer,
                ValidAudience = Audience,
                ValidateIssuerSigningKey = true,
                ValidateLifetime = true,
                IssuerSigningKey = GetSymmetricSecurityKey(),
                ClockSkew = TimeSpan.FromMilliseconds(ClockSkewMS)
            };
        }

        public static SigningCredentials GetSigningCredentials()
        {
            return new SigningCredentials(GetSymmetricSecurityKey(), SecurityAlgorithms.HmacSha256Signature);
        }
    }
}
