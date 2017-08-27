using QuantumLogic.Core.Authorization;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Policy.Main;

namespace QuantumLogic.WebApi.Policy.Main
{
    public class UserPolicy : NullEntityPolicy<User, int>, IUserPolicy
    {
        #region Ctors

        public UserPolicy(IQLPermissionChecker permissionChecker, IQLSession session)
            : base(permissionChecker, session)
        { }

        #endregion
    }
}
