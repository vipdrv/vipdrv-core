namespace QuantumLogic.Core.Utils.Vehicles.Infos
{
    public class VehicleYearInfo
    {
        public int Value { get; set; }
        public int Count { get; set; }

        #region Ctors

        public VehicleYearInfo()
        { }

        public VehicleYearInfo(int value, int count)
        {
            Value = value;
            Count = count;
        }

        #endregion
    }
}
