using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Validation.Widget;

namespace QuantumLogic.WebApi.Validation.Widget
{
    public class VehicleValidationService : NullEntityValidationService<Vehicle, int>, IVehicleValidationService
    {
        #region Ctors

        public VehicleValidationService()
            : base()
        { }

        #endregion
    }
}
