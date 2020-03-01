namespace QuantumLogic.WebApi.Configurations.Logging
{
    public class LoggingConfiguration
    {
        public string DefaultLevel { get; set; }
        public SeqServerConfiguration SeqServer { get; set; }
        public FileLoggerConfiguration FileLogger { get; set; }
    }
}
