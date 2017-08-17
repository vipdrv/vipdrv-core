using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Site : Entity<int>, IValidable
    {
        #region Fields
        public int UserId { get; set; }
        public string Name { get; set; }
        public string Url { get; set; }
        public string Contacts { get; set; }
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
