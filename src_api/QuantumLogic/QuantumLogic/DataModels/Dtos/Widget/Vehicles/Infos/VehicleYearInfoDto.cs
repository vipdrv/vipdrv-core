using QuantumLogic.Core.Utils.Vehicles.Infos;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Vehicles.Infos
{
    public class VehicleYearInfoDto
    {
        public string Name { get; set; }
        public int Count { get; set; }

        #region Ctors

        public VehicleYearInfoDto()
        { }

        public VehicleYearInfoDto(VehicleYearInfo entity)
        {
            Name = entity.Value.ToString();
            Count = entity.Count;
        }

        #endregion
    }
}
