namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class VehicleMake : Entity<int>
    {
        public string Alias { get; set; }
        public string DisplayText { get; set; }
        public string ImageUrl { get; set; }

        #region Ctors

        public VehicleMake()
            : base()
        { }

        #endregion
    }
}
