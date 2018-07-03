using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Enums;
using QuantumLogic.Core.Extensions;
using System;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Sites.Import
{
    public class ImportVehiclesShapshot
    {
        public string TimestampUtc { get; private set; }
        public string TimeElapsed { get; private set; }
        public string Status { get; private set; }
        public string Message { get; private set; }
        public IEnumerable<ImportVehiclesForSiteResultDto> ImportVehiclesForSiteResults { get; private set; }

        #region Ctors

        public ImportVehiclesShapshot(
            TimeSpan timeElapsed,
            ImportStatusEnum status,
            string message,
            IEnumerable<ImportVehiclesForSiteResultDto> importResults)
        {
            TimestampUtc = DateTime.UtcNow.FormatUtcDateTimeToUserFriendlyString();
            TimeElapsed = timeElapsed.FormatTimeSpanToUserFriendlyLongString();
            Status = status.ToString();
            Message = message;
            ImportVehiclesForSiteResults = importResults;
        }

        #endregion
    }
}
