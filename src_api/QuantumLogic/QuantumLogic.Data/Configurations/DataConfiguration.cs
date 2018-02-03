using QuantumLogic.Data.Configurations.Connection;

namespace QuantumLogic.Data.Configurations
{
    /// <summary>
    /// Is used to store Data Application's options (configurations)
    /// </summary>
    public class DataConfiguration
    {
        public ConnectionConfiguration Connection { get; set; }
    }
}
