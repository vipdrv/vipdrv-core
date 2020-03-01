using QuantumLogic.Core.Configurations;
using QuantumLogic.Data.Configurations;
using QuantumLogic.WebApi.Configurations.Logging;
using QuantumLogic.WebApi.Configurations.Reporting;

namespace QuantumLogic.WebApi.Configurations
{
    /// <summary>
    /// Is used to store all QuantumLogic's options (configurations)
    /// </summary>
    public class QuantumLogicConfiguration
    {
        public ApplicationInfoConfiguration ApplicationInfo { get; set; }
        public WebApiConfiguration WebApi { get; set; }
        public CoreConfiguration Core { get; set; }
        public DataConfiguration Data { get; set; }
        public LoggingConfiguration Logging { get; set; }
        public RemoteReportingConfiguration RemoteReporting { get; set; }
    }
}
