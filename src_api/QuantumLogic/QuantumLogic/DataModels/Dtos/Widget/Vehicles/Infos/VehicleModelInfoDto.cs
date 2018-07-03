using QuantumLogic.Core.Utils.Vehicles.Infos;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Vehicles.Infos
{
    public class VehicleModelInfoDto
    {
        public string Name { get; set; }
        public int Count { get; set; }
        public string ImageUrl { get; set; }

        #region Ctors

        public VehicleModelInfoDto()
        { }

        public VehicleModelInfoDto(VehicleModelInfo entity)
        {
            Name = entity.Name;
            Count = entity.Count;
            ImageUrl = entity.ImageUrl;
        }

        #endregion
    }
}
