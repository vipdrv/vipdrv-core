using System;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Sites.Import
{
    public class ImportVehiclesShapshot
    {
        public DateTime TimestampUtc { get; private set; }
        public TimeSpan TimeElapsed { get; private set; }
        public IEnumerable<ImportVehiclesForSiteResultDto> ImportVehiclesForSiteResults { get; private set; }

        #region Ctors

        public ImportVehiclesShapshot(TimeSpan timeElapsed, IEnumerable<ImportVehiclesForSiteResultDto> importResults)
        {
            TimestampUtc = DateTime.UtcNow;
            TimeElapsed = timeElapsed;
            ImportVehiclesForSiteResults = importResults;
        }

        #endregion
    }
}
