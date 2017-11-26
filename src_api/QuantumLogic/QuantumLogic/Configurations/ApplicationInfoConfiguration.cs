using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Configurations
{
    /// <summary>
    /// Class is used to describe Application
    /// </summary>
    public class ApplicationInfoConfiguration
    {
        /// <summary>
        /// Application Name
        /// </summary>
        public string Name { get; set; }
        /// <summary>
        /// Application version (format: {major}.{minor}.{patch})
        /// </summary>
        public string Version { get; set; }
        /// <summary>
        /// Is used like mask to show latest build number in TeamCity
        /// </summary>
        public string BuildCounterMask { get; set; }
    }
}
