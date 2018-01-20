using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Validation.Main;

namespace QuantumLogic.WebApi.Validation.Main
{
    public class RoleValidationService : NullEntityValidationService<Role, int>, IRoleValidationService
    {
        #region Ctors

        public RoleValidationService()
            : base()
        { }

        #endregion
    }
}
