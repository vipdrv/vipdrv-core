using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Validation.Main;

namespace QuantumLogic.WebApi.Validation.Main
{
    public class UserValidationService : NullEntityValidationService<User, int>, IUserValidationService
    {
        #region Ctors

        public UserValidationService()
            : base()
        { }

        #endregion
    }
}
