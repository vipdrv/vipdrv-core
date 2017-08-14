using QuantumLogic.Core.Configurations;
using QuantumLogic.Data.Configurations;

namespace QuantumLogic.WebApi.Configurations
{
    /// <summary>
    /// Is used to store all QuantumLogic's options (configurations)
    /// </summary>
    public class QuantumLogicConfiguration
    {
        public WebApiConfiguration WebApi { get; set; }
        public CoreConfiguration Core { get; set; }
        public DataConfiguration Data { get; set; }
    }
}
