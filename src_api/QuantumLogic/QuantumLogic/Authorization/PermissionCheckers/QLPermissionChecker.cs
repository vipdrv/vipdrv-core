using QuantumLogic.Core.Authorization;
using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Http;
using QuantumLogic.WebApi.DataModels.Responses.Authorization;
using QuantumLogic.WebApi.Controllers.Authorization;

namespace QuantumLogic.WebApi.Authorization.PermissionCheckers
{
    public class QLPermissionChecker : IQLPermissionChecker
    {
        protected readonly IList<string> CurrentUserGrantedPermissions;

        public QLPermissionChecker(IHttpContextAccessor contextAccessor)
        {
            CurrentUserGrantedPermissions = GetCurrecntUserGrantedPermissions(
                contextAccessor.HttpContext.User.Claims.ToDictionary((item) => item.Type, (item) => item.Value)).Result;
        }

        protected virtual async Task<IList<string>> GetCurrecntUserGrantedPermissions(IDictionary<string, string> userClaims)
        {
            try
            {
                IdentityInfo identityInfo =
                    await AuthorizationController.ParseIdentityInfoFromIdentityClaimsAsync(userClaims);
                return identityInfo.GrantedPermissions;
            }
            catch (ArgumentException)
            {
                return new List<string>();
            }
        }

        public Task<bool> IsGrantedAsync(string permissionId)
        {
            return Task.FromResult(IsGranted(permissionId));
        }

        public Task<bool> IsGrantedAsync(long userId, string permissionId)
        {
            throw new NotSupportedException();
        }

        public bool IsGranted(string permissionId)
        {
            return CurrentUserGrantedPermissions.Contains(permissionId);
        }
    }
}
