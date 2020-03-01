namespace QuantumLogic.WebApi.DataModels.Dtos.Shared.Error
{
    public class PatchFailureDto : FailureDto
    {
        public string Path { get; set; }
        public string Operation { get; set; }
        public object Value { get; set; }

        #region Ctors

        protected PatchFailureDto()
            : base()
        { }

        public PatchFailureDto(string operation, string path, object value, string message)
            : base(message)
        {
            Operation = operation;
            Path = path;
            Value = value;
        }

        #endregion
    }
}
