namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Beverage : Entity<int>, IPassivable, IOrderable, IValidable, IUpdatableFrom<Beverage>
    {
        #region Fields

        public int SiteId { get; set; }
        public string Name { get; set; }
        public string Description { get; set; }
        public int Order { get; set; }
        public bool IsActive { get; set; }
        public string PhotoUrl { get; set; }

        #endregion

        #region Relations

        public virtual Site Site { get; set; }

        #endregion

        #region Ctors

        public Beverage()
            : base()
        { }

        public Beverage(int id, int siteId, string name, string description, int order, bool isActive, string photoUrl)
            : this()
        {
            Id = id;
            SiteId = siteId;
            Name = name;
            Description = description;
            Order = order;
            IsActive = isActive;
            PhotoUrl = photoUrl;
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

        public void UpdateFrom(Beverage actualEntity)
        {
            SiteId = actualEntity.SiteId;
            Name = actualEntity.Name;
            Description = actualEntity.Description;
            Order = actualEntity.Order;
            IsActive = actualEntity.IsActive;
            PhotoUrl = actualEntity.PhotoUrl;
        }

        #endregion
    }
}
