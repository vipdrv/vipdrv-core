using QuantumLogic.Core.Utils.VehicleMakes;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.VehicleMakes
{
    public class VehicleMakeDto
    {
        public string Name { get; set; }
        public int Count { get; set; }
        public string ImageUrl { get; set; }

        #region Ctors

        public VehicleMakeDto()
        { }

        public VehicleMakeDto(VehicleMake vehicleMake)
        {
            Name = vehicleMake.Name;
            Count = vehicleMake.Count;
            ImageUrl = vehicleMake.ImageUrl;
        }

        #endregion
    }
}
