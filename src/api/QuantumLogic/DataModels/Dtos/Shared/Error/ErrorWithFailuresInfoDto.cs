using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Dtos.Shared.Error
{
    public class ErrorWithFailuresInfoDto<TFailure> : ErrorDto
        where TFailure : FailureDto
    {
        public List<TFailure> Failures { get; set; }

        #region Ctors

        protected ErrorWithFailuresInfoDto()
            : base()
        { }

        public ErrorWithFailuresInfoDto(string requestIdentifier, string message, List<TFailure> failures)
            : base(requestIdentifier, message)
        {
            Failures = failures;
        }

        #endregion
    }
}
