using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Models;
using System;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Vehicles
{
    public class VehicleImportResultDto
    {
        public DateTime TimestampUtc { get; private set; }
        public TimeSpan TimeElapsed { get; private set; }
        public IEnumerable<VehicleImportForSiteResult> ImportForSiteResults { get; private set; }

        #region Ctors

        public VehicleImportResultDto(TimeSpan timeElapsed, IEnumerable<VehicleImportForSiteResult> importForSiteResults)
        {
            TimestampUtc = DateTime.UtcNow;
            TimeElapsed = timeElapsed;
            ImportForSiteResults = importForSiteResults;
        }

        #endregion
    }
}
