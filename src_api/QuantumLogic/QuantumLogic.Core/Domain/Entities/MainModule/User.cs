using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Domain.Entities.MainModule
{
    public class User : Entity<int>, IValidable
    {
        #region Fields
        public string Email { get; set; }
        public string Password { get; set; }
        public int MaxSitesCount { get; set; }
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
