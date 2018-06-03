namespace QuantumLogic.Core.Utils.Vehicles.Infos
{
    public class VehicleMakeInfo
    {
        public string Name { get; set; }
        public int Count { get; set; }
        public string ImageUrl { get; set; }

        #region Ctors

        public VehicleMakeInfo()
        { }

        public VehicleMakeInfo(string name, int count, string imageUrl)
        {
            Name = name;
            Count = count;
            ImageUrl = imageUrl;
        }

        #endregion
    }
}
