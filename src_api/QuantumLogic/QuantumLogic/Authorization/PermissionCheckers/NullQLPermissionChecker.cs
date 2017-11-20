using QuantumLogic.Core.Authorization;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Authorization.PermissionCheckers
{
    /// <summary>
    /// Is used as null (no deny access) permisiion checker
    /// </summary>
    public sealed class NullQLPermissionChecker : IQLPermissionChecker
    {
        public Task<bool> IsGrantedAsync(string permissionId)
        {
            return Task.FromResult(true);
        }

        public Task<bool> IsGrantedAsync(long userId, string permissionId)
        {
            return Task.FromResult(true);
        }
    }
}
