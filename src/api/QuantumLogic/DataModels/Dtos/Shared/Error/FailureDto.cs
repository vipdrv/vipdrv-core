namespace QuantumLogic.WebApi.DataModels.Dtos.Shared.Error
{
    public class FailureDto
    {
        public string Message { get; set; }

        #region Ctors

        protected FailureDto()
        { }

        public FailureDto(string message)
        {
            Message = message;
        }

        #endregion
    }
}
