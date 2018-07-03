using QuantumLogic.Core.Utils.Vehicles;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Vehicles.Infos;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.WebApi.DataModels.Responses.Widget.Vehicles
{
    public class VehicleMakesDto
    {
        public IEnumerable<VehicleMakeInfoDto> New { get; set; }
        public IEnumerable<VehicleMakeInfoDto> Used { get; set; }

        #region Ctors

        public VehicleMakesDto()
        { }

        public VehicleMakesDto(IEnumerable<VehicleMakeInfoDto> @new, IEnumerable<VehicleMakeInfoDto> used)
        {
            New = @new.OrderByDescending(r => r.Count);
            Used = used.OrderByDescending(r => r.Count);
        }

        public VehicleMakesDto(VehicleMakesModel model)
            : this(model.New.Select(r => new VehicleMakeInfoDto(r)), model.Used.Select(r => new VehicleMakeInfoDto(r)))
        { }

        #endregion
    }
}
