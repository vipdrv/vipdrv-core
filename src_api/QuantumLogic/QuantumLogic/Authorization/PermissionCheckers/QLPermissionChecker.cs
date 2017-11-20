using QuantumLogic.Core.Authorization;
using System;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Authorization.PermissionCheckers
{
    public class QLPermissionChecker : IQLPermissionChecker
    {
        public Task<bool> IsGrantedAsync(string permissionId)
        {
            throw new NotImplementedException();
        }

        public Task<bool> IsGrantedAsync(long userId, string permissionId)
        {
            throw new NotImplementedException();
        }
    }
}
