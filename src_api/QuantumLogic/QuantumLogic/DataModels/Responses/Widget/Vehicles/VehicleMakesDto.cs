using QuantumLogic.Core.Utils.VehicleMakes;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Responses.Widget.Vehicles
{
    public class VehicleMakesDto
    {
        public IEnumerable<string> New { get; set; }
        public IEnumerable<string> Used { get; set; }

        #region Ctors

        public VehicleMakesDto()
        { }

        public VehicleMakesDto(IEnumerable<string> @new, IEnumerable<string> used)
        {
            New = @new;
            Used = used;
        }

        public VehicleMakesDto(VehicleMakesModel model)
            : this(model.New, model.Used)
        { }

        #endregion
    }
}
