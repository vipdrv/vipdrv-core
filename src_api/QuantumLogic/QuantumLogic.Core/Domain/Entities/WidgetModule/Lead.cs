using System;

namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Lead : Entity<int>, IValidable, IUpdatableFrom<Lead>
    {
        #region Fields

        public int SiteId { get; set; }
        public int ExpertId { get; set; }
        public int BeverageId { get; set; }
        public int RouteId { get; set; }
        public DateTime Recieved { get; set; }
        public int UserName { get; set; }
        public string UserPhone { get; set; }
        public string UserEmail { get; set; }

        #endregion

        #region Relations

        public virtual Site Site { get; set; }

        #endregion

        #region Ctors

        public Lead()
            : base()
        { }

        public Lead(int id, int siteId, int expertId, int beverageId, int routeId, DateTime recieved, int userName, string userPhone, string userEmail)
            : this()
        {
            Id = id;
            SiteId = siteId;
            ExpertId = expertId;
            BeverageId = beverageId;
            RouteId = routeId;
            Recieved = recieved;
            UserName = userName;
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
            Recieved = actualEntity.Recieved;
            UserName = actualEntity.UserName;
            UserPhone = actualEntity.UserPhone;
            UserEmail = actualEntity.UserEmail;
        }

        #endregion
    }
}
