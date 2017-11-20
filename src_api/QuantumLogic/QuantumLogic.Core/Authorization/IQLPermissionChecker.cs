using System.Threading.Tasks;

namespace QuantumLogic.Core.Authorization
{
    /// <summary>
    /// Is used as permission checker for QuantumLogic application
    /// </summary>
    public interface IQLPermissionChecker
    {
        /// <summary>
        /// Is used to checks if current user is granted for a permission.
        /// </summary>
        /// <param name="permissionId">permission identifier</param>
        /// <returns>predicate result</returns>
        Task<bool> IsGrantedAsync(string permissionId);
        /// <summary>
        /// Is used to checks if user is granted for a permission.
        /// </summary>
        /// <param name="userId">user identifier</param>
        /// <param name="permissionId">permission identifier</param>
        /// <returns>predicate result</returns>
        Task<bool> IsGrantedAsync(long userId, string permissionId);
    }
}
