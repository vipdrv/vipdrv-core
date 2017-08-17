using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Lead : Entity<int>, IValidable
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

        public Lead()
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
