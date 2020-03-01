namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Expert : Entity<int>, IPassivable, IOrderable, IValidable, IUpdatableFrom<Expert>
    {
        #region Fields

        public int SiteId { get; set; }
        public string PhotoUrl { get; set; }
        public string Name { get; set; }
        public string Title { get; set; }
        public string Description { get; set; }
        public string Email { get; set; }
        public string PhoneNumber { get; set; }
        public string FacebookUrl { get; set; }
        public string LinkedinUrl { get; set; }
        public string WorkingHours { get; set; }
        public int Order { get; set; }
        public bool IsActive { get; set; }

        public bool IsPartOfTeamNewCars { get; set; }
        public bool IsPartOfTeamUsedCars { get; set; }
        public bool IsPartOfTeamCPO { get; set; }

        public string EmployeeId { get; set; }

        public string DealerraterUrl { get; set; }

        #endregion

        #region Relations

        public virtual Site Site { get; set; }

        #endregion

        #region Ctors

        public Expert()
            : base()
        { }

        public Expert(int id, int siteId, string name, string description, int order, bool isActive, string photoUrl, string facebookUrl, string linkedinUrl, string workingHours)
            : this()
        {
            Id = id;
            SiteId = siteId;
            Name = name;
            Description = description;
            Order = order;
            IsActive = isActive;
            PhotoUrl = photoUrl;
            FacebookUrl = facebookUrl;
            LinkedinUrl = linkedinUrl;
            WorkingHours = workingHours;
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

        public void UpdateFrom(Expert actualEntity)
        {
            SiteId = actualEntity.SiteId;
            PhotoUrl = actualEntity.PhotoUrl;
            Name = actualEntity.Name;
            Title = actualEntity.Title;
            Description = actualEntity.Description;
            Email = actualEntity.Email;
            PhoneNumber = actualEntity.PhoneNumber;
            Order = actualEntity.Order;
            IsActive = actualEntity.IsActive;
            FacebookUrl = actualEntity.FacebookUrl;
            LinkedinUrl = actualEntity.LinkedinUrl;
            WorkingHours = actualEntity.WorkingHours;
            IsPartOfTeamNewCars = actualEntity.IsPartOfTeamNewCars;
            IsPartOfTeamUsedCars = actualEntity.IsPartOfTeamUsedCars;
            IsPartOfTeamCPO = actualEntity.IsPartOfTeamCPO;
            EmployeeId = actualEntity.EmployeeId;
            DealerraterUrl = actualEntity.DealerraterUrl;
        }

        #endregion
    }
}
