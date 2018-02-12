namespace QuantumLogic.WebApi.DataModels.Dtos.Shared.Error
{
    public class ValidationFailureDto : FailureDto
    {
        public string Severity { get; set; }
        public string PropertyName { get; set; }

        #region Ctors

        protected ValidationFailureDto()
            : base()
        { }

        public ValidationFailureDto(string propertyName, string severity, string message)
            : base(message)
        {
            PropertyName = propertyName;
            Severity = severity;
        }

        #endregion
    }
}
