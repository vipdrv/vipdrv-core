using QuantumLogic.Core.Utils.Vehicles.Infos;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Vehicles.Infos
{
    public class VehicleMakeInfoDto
    {
        public string Name { get; set; }
        public int Count { get; set; }
        public string ImageUrl { get; set; }

        #region Ctors

        public VehicleMakeInfoDto()
        { }

        public VehicleMakeInfoDto(VehicleMakeInfo entity)
        {
            Name = entity.Name;
            Count = entity.Count;
            ImageUrl = entity.ImageUrl;
        }

        #endregion
    }
}
