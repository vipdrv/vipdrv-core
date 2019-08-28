namespace QuantumLogic.Core.Utils.Vehicles.Infos
{
    public class VehicleModelInfo
    {
        public string Name { get; set; }
        public int Count { get; set; }
        public string ImageUrl { get; set; }

        #region Ctors

        public VehicleModelInfo()
        { }

        public VehicleModelInfo(string name, int count, string imageUrl)
        {
            Name = name;
            Count = count;
            ImageUrl = imageUrl;
        }

        #endregion
    }
}
