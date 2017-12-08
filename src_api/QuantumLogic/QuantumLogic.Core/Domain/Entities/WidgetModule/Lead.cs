using System;

namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Lead : Entity<int>, IValidable, IUpdatableFrom<Lead>
    {
        #region Fields

        public int SiteId { get; set; }
        public int ExpertId { get; set; }
        public int? BeverageId { get; set; }
        public int RouteId { get; set; }
        public DateTime RecievedUtc { get; set; }
        public string FirstName { get; set; }
        public string SecondName { get; set; }
        public string UserPhone { get; set; }
        public string UserEmail { get; set; }
        public bool IsNew { get; set; }
        public bool IsReachedByManager { get; set; }

        #endregion

        #region Relations

        public virtual Site Site { get; set; }
        public virtual Route Route { get; set; }
        public virtual Expert Expert { get; set; }
        public virtual Beverage Beverage { get; set; }

        #endregion

        public string FullName
        {
            get
            {
                return FirstName == null && SecondName == null ? String.Empty : $"{FirstName} {SecondName}";
            }
        }

        #region Ctors

        public Lead()
            : base()
        { }

        public Lead(int id, int siteId, int expertId, int beverageId, int routeId, DateTime recieved, string firstname, string secondName, string userPhone, string userEmail)
            : this()
        {
            Id = id;
            SiteId = siteId;
            ExpertId = expertId;
            BeverageId = beverageId;
            RouteId = routeId;
            RecievedUtc = recieved;
            FirstName = firstname;
            SecondName = secondName;
            UserPhone = userPhone;
            UserEmail = userEmail;
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

        public void UpdateFrom(Lead actualEntity)
        {
            SiteId = actualEntity.SiteId;
            ExpertId = actualEntity.ExpertId;
            BeverageId = actualEntity.BeverageId;
            RouteId = actualEntity.RouteId;
            FirstName = actualEntity.FirstName;
            SecondName = actualEntity.SecondName;
            UserPhone = actualEntity.UserPhone;
            UserEmail = actualEntity.UserEmail;
        }

        #endregion
    }
}
