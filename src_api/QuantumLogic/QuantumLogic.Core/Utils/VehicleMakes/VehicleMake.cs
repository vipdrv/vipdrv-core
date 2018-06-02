namespace QuantumLogic.Core.Utils.VehicleMakes
{
    public class VehicleMake
    {
        public string Name { get; set; }
        public int Count { get; set; }
        public string ImageUrl { get; set; }

        #region Ctors

        public VehicleMake()
        { }

        public VehicleMake(string name, int count, string imageUrl)
        {
            Name = name;
            Count = count;
            ImageUrl = imageUrl;
        }

        #endregion
    }
}
