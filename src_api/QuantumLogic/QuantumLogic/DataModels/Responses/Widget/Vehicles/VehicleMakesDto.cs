using QuantumLogic.Core.Utils.VehicleMakes;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.VehicleMakes;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.WebApi.DataModels.Responses.Widget.Vehicles
{
    public class VehicleMakesDto
    {
        public IEnumerable<VehicleMakeDto> New { get; set; }
        public IEnumerable<VehicleMakeDto> Used { get; set; }

        #region Ctors

        public VehicleMakesDto()
        { }

        public VehicleMakesDto(IEnumerable<VehicleMakeDto> @new, IEnumerable<VehicleMakeDto> used)
        {
            New = @new.OrderByDescending(r => r.Count);
            Used = used.OrderByDescending(r => r.Count);
        }

        public VehicleMakesDto(VehicleMakesModel model)
            : this(model.New.Select(r => new VehicleMakeDto(r)), model.Used.Select(r => new VehicleMakeDto(r)))
        { }

        #endregion
    }
}
