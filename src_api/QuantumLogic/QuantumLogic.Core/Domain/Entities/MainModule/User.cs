using System;
using System.Collections.Generic;
using System.Text;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Domain.Entities.MainModule
{
    public class User : Entity<int>, IValidable
    {
        #region Fields
        public string Email { get; set; }
        public string Password { get; set; }
        public int MaxSitesCount { get; set; }
        #endregion

        #region relations
        public virtual ICollection<Site> Sites { get; set; }
        #endregion

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
