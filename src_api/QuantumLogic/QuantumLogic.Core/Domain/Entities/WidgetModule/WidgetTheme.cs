using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class WidgetTheme : Entity<int>, IValidable
    {
        #region Fields
        public int SiteId { get; set; }
        public string CssUrl { get; set; }
        public string ButtonImageUrl { get; set; }
        #endregion

        #region relations
        public virtual Site Site { get; set; }
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
