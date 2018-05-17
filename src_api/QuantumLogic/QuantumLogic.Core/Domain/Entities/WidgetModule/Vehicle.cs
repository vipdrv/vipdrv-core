namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Vehicle : Entity<int>, IValidable, IUpdatableFrom<Vehicle>
    {
        #region Fields

        public int SiteId { get; set; }
        public string Title { get; set; }
        public string ImageUrl { get; set; }
        public int Year { get; set; }
        public string Make { get; set; }
        public string Model { get; set; }
        public VehicleConditions Condition { get; set; }
        public string VIN { get; set; }
        public string Stock { get; set; }
        public string VIN { get; set; }
        public string Stock { get; set; }
        public string Trim { get; set; }


        #endregion

        #region Relations

        public virtual Site Site { get; set; }

        #endregion

        #region Ctors

        public Vehicle() : base()
        {
        }

        public Vehicle(
            int siteId,
            string title, 
            string imageUrl, 
            int year, 
            string make, 
            string model,
            VehicleConditions condition, 
            string vin, 
            string stock)
            : base()
        {
            SiteId = siteId;
            Title = title;
            ImageUrl = imageUrl;
            Year = year;
            Make = make;
            Model = model;
            Condition = condition;
            Vin = vin;
            Stock = stock;
        }

        #endregion

        #region IValidable implementation

        public bool IsValid()
        {
            return InnerValidate(false);
        }
        public void Validate()
        {
            InnerValidate(true);
        }
        protected virtual bool InnerValidate(bool throwException)
        {
            return true;
        }

        #endregion

        #region IUpdatable implementation

        public void UpdateFrom(Vehicle actualEntity)
        {
            SiteId = actualEntity.SiteId;
            Title = actualEntity.Title;
            ImageUrl = actualEntity.ImageUrl;
            Year = actualEntity.Year;
            Make = actualEntity.Make;
            Model = actualEntity.Model;
            Condition = actualEntity.Condition;
            VIN = actualEntity.VIN;
            Stock = actualEntity.Stock;
            Trim = actualEntity.Trim;
        }

        #endregion
    }

    public enum VehicleConditions : byte
    {
        Undefined = 0,
        New = 1,
        Used = 2
    }
}
