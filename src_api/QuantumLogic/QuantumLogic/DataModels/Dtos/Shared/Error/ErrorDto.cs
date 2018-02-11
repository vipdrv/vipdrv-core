namespace QuantumLogic.WebApi.DataModels.Dtos.Shared.Error
{
    public class ErrorDto
    {
        public string RequestIdentifier { get; set; }
        public string Message { get; set; }

        #region Ctors

        protected ErrorDto()
        { }

        public ErrorDto(string requestIdentifier, string message)
        {
            RequestIdentifier = requestIdentifier;
            Message = message;
        }

        #endregion
    }
}
