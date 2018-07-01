using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Enums;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Models
{
    public class VehicleImportForSiteResult
    {
        public ImportStatusEnum Status { get; private set; }
        public string Message { get; private set; }
        public int SiteId { get; private set; }
        public string SiteName { get; private set; }
        public int ProcessedEntitiesCount { get; set; }

        #region Ctors

        public VehicleImportForSiteResult(int siteId, string siteName, string message)
        {
            Status = ImportStatusEnum.Failed;
            SiteId = siteId;
            SiteName = siteName;
            Message = message;
        }

        public VehicleImportForSiteResult(int siteId, string siteName, int processedEntitiesCount, string message = null)
        {
            Status = ImportStatusEnum.Success;
            SiteId = siteId;
            SiteName = siteName;
            ProcessedEntitiesCount = processedEntitiesCount;
            Message = message;
        }

        #endregion
    }
}
