using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Enums;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Models
{
    public class ImportVehiclesForSiteResult
    {
        public ImportStatusEnum Status { get; private set; }
        public string Message { get; private set; }
        public int SiteId { get; private set; }
        public string SiteName { get; private set; }
        public int ProcessedVehiclesCount { get; private set; }

        #region Ctors

        public ImportVehiclesForSiteResult(int siteId, string siteName, string message, ImportStatusEnum status = ImportStatusEnum.Failed)
        {
            Status = status;
            SiteId = siteId;
            SiteName = siteName;
            Message = message;
        }

        public ImportVehiclesForSiteResult(int siteId, string siteName, int processedVehiclesCount, string message = null)
        {
            Status = ImportStatusEnum.Success;
            SiteId = siteId;
            SiteName = siteName;
            ProcessedVehiclesCount = processedVehiclesCount;
            Message = message;
        }

        #endregion
    }
}
