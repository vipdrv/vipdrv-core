using QuantumLogic.Core.Authorization;
using System;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Authorization.PermissionCheckers
{
    public class QLPermissionChecker : IQLPermissionChecker
    {
        protected readonly ISet<string> CurrentUserGrantedPermissions;
        
        public QLPermissionChecker(IQLSession session)
        {
            CurrentUserGrantedPermissions = session.GrantedPermissions;
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
