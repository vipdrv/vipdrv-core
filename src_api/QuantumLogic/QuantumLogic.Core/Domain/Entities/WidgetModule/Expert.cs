﻿using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class Expert : Entity<int>, IValidable
    {
        #region Fields

        public int SiteId { get; set; }
        public string Name { get; set; }
        public string Description { get; set; }
        public int Order { get; set; }
        public bool IsActive { get; set; }
        public string PhotoUrl { get; set; }
        public string FacebookUrl { get; set; }
        public string LinkedinUrl { get; set; }
        public string WorkingHours { get; set; }
        
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
        }
    }
}