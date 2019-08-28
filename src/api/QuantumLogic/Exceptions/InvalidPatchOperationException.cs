using QuantumLogic.WebApi.DataModels.Dtos.Shared.Error;
using System;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.Exceptions
{
    public class InvalidPatchOperationException : BadRequestException
    {
        public override string UserfriendlyMessage
        {
            get
            {
                return "Invalid request: patch document contains not valid recept.";
            }
        }

        public IEnumerable<PatchFailureDto> PatchFailures { get; private set; }

        #region Ctors

        public InvalidPatchOperationException(IEnumerable<PatchFailureDto> patchFailures)
            : base()
        {
            PatchFailures = patchFailures;
        }

        public InvalidPatchOperationException(string message, IEnumerable<PatchFailureDto> patchFailures)
            : base(message)
        {
            PatchFailures = patchFailures;
        }

        public InvalidPatchOperationException(string message, Exception innerException, IEnumerable<PatchFailureDto> patchFailures)
            : base(message, innerException)
        {
            PatchFailures = patchFailures;
        }

        #endregion
    }
}
