using QuantumLogic.Core.Domain.Entities.MainModule;
using System.Collections.Generic;

namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Site : Entity<int>, IValidable, IUpdatableFrom<Site>
    {
        #region Fields

        public int UserId { get; set; }
        public string BeautyId { get; set; }
        public string Name { get; set; }
        public string Url { get; set; }
        public string Contacts { get; set; }
        public string ImageUrl { get; set; }
        public string DealerName { get; set; }

        #endregion

        #region Relation

        public virtual User User { get; set; }
        public virtual ICollection<Beverage> Beverages { get; set; }
        public virtual ICollection<Expert> Experts { get; set; }
        public virtual ICollection<Route> Routes { get; set; }
        public virtual ICollection<Lead> Leads { get; set; }
        public virtual WidgetTheme WidgetTheme { get; set; }

        #endregion

        #region Ctors

        public Site()
            : base()
        {
            Beverages = new HashSet<Beverage>();
            Experts = new HashSet<Expert>();
            Routes = new HashSet<Route>();
            Leads = new HashSet<Lead>();
        }

        public Site(int id, int userId, string beautyId, string name, string url, string contacts)
            : this()
        {
            Id = id;
            UserId = userId;
            BeautyId = beautyId;
            Name = name;
            Url = url;
            Contacts = contacts;
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

        public void UpdateFrom(Site actualEntity)
        {
            UserId = actualEntity.UserId;
            BeautyId = actualEntity.BeautyId;
            Name = actualEntity.Name;
            Url = actualEntity.Url;
            Contacts = actualEntity.Contacts;
            ImageUrl = actualEntity.ImageUrl;
        }

        #endregion
    }
}
