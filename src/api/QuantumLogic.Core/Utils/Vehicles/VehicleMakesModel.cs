using QuantumLogic.Core.Utils.Vehicles.Infos;
using System.Collections.Generic;

namespace QuantumLogic.Core.Utils.Vehicles
{
    public class VehicleMakesModel
    {
        public IEnumerable<VehicleMakeInfo> New { get; set; }
        public IEnumerable<VehicleMakeInfo> Used { get; set; }

        #region Ctors

        public VehicleMakesModel()
        { }

        public VehicleMakesModel(IEnumerable<VehicleMakeInfo> @new, IEnumerable<VehicleMakeInfo> used)
        {
            New = @new;
            Used = used;
        }

        #endregion
    }
}
