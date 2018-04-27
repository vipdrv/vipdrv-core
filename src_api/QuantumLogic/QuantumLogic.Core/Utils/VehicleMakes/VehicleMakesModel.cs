using System.Collections.Generic;

namespace QuantumLogic.Core.Utils.VehicleMakes
{
    public class VehicleMakesModel
    {
        public IEnumerable<string> New { get; set; }
        public IEnumerable<string> Used { get; set; }

        #region Ctors

        public VehicleMakesModel()
        { }

        public VehicleMakesModel(IEnumerable<string> @new, IEnumerable<string> used)
        {
            New = @new;
            Used = used;
        }

        #endregion
    }
}
