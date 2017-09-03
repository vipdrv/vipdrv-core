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
        public string Username { get; set; }
        public string UserPhone { get; set; }
        public string UserEmail { get; set; }

        #endregion

        #region Relations

        public virtual Site Site { get; set; }
        public virtual Route Route { get; set; }
        public virtual Expert Expert { get; set; }
        public virtual Beverage Beverage { get; set; }

        #endregion

        #region Ctors

        public Lead()
            : base()
        { }

        public Lead(int id, int siteId, int expertId, int beverageId, int routeId, DateTime recieved, string username, string userPhone, string userEmail)
            : this()
        {
            Id = id;
            SiteId = siteId;
            ExpertId = expertId;
            BeverageId = beverageId;
            RouteId = routeId;
            RecievedUtc = recieved;
            Username = username;
            UserPhone = userPhone;
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
            Username = actualEntity.Username;
            UserPhone = actualEntity.UserPhone;
            UserEmail = actualEntity.UserEmail;
        }

        #endregion
    }
}
