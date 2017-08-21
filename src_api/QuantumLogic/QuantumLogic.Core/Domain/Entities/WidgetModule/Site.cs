using System;
using System.Collections.Generic;
using System.Text;
using QuantumLogic.Core.Domain.Entities.MainModule;

namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Site : Entity<int>, IValidable
    {
        #region Fields
        public int UserId { get; set; }
        public string BeautyId { get; set; }
        public string Name { get; set; }
        public string Url { get; set; }
        public string Contacts { get; set; }
        #endregion

        #region Relation
        public virtual User User { get; set; }
        public virtual ICollection<Beverage> Beverages { get; set; }
        public virtual ICollection<Expert> Experts { get; set; }
        public virtual ICollection<Route> Routes { get; set; }
        public virtual ICollection<Lead> Leads { get; set; }
        public virtual WidgetTheme WidgetTheme { get; set; }
        #endregion

        public Site()
        {
        }

        public bool IsValid()
        {
            throw new NotImplementedException();
        }

        public void Validate()
        {
            throw new NotImplementedException();
        }
    }
}
