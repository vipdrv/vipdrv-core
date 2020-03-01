namespace QuantumLogic.WebApi.Configurations.Logging
{
    public class FileLoggerConfiguration
    {
        public bool Enabled { get; set; }
        public string Level { get; set; }
        public bool IsGlobalPath { get; set; }
        public string Path { get; set; }
        public long? FileSizeLimitBytes { get; set; }
        public int? RetainedFileCountLimit { get; set; }
    }
}
