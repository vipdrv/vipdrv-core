using System.Collections.Generic;

namespace QuantumLogic.Core.Utils.VehicleMakes
{
    public class VehicleMakesModel
    {
        public IEnumerable<VehicleMake> New { get; set; }
        public IEnumerable<VehicleMake> Used { get; set; }

        #region Ctors

        public VehicleMakesModel()
        { }

        public VehicleMakesModel(IEnumerable<VehicleMake> @new, IEnumerable<VehicleMake> used)
        {
            New = @new;
            Used = used;
        }

        #endregion
    }
}
