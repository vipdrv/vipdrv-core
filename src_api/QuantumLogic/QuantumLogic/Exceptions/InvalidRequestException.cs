using QuantumLogic.WebApi.DataModels.Dtos.Shared.Error;
using System;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.Exceptions
{
    public class InvalidRequestException : BadRequestException
    {
        public override string UserfriendlyMessage
        {
            get
            {
                return "Invalid request: request has syntax errors.";
            }
        }

        public IEnumerable<ValidationFailureDto> ValidationFailures { get; private set; }

        #region Ctors

        public InvalidRequestException(IEnumerable<ValidationFailureDto> validationFailures)
            : base()
        {
            ValidationFailures = validationFailures;
        }

        public InvalidRequestException(string message, IEnumerable<ValidationFailureDto> validationFailures)
            : base(message)
        {
            ValidationFailures = validationFailures;
        }

        public InvalidRequestException(string message, Exception innerException, IEnumerable<ValidationFailureDto> validationFailures)
            : base(message, innerException)
        {
            ValidationFailures = validationFailures;
        }

        #endregion
    }
}
