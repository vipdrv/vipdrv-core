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
        public int ProcessedNewVehiclesCount { get; private set; }
        public int ProcessedUsedVehiclesCount { get; private set; }

        #region Ctors

        private ImportVehiclesForSiteResult(ImportStatusEnum status, int siteId, string siteName, string message)
        {
            Status = status;
            SiteId = siteId;
            SiteName = siteName;
            Message = message;
        }

        public ImportVehiclesForSiteResult(int siteId, string siteName, string message, ImportStatusEnum status = ImportStatusEnum.Failed)
            : this(status, siteId, siteName, message)
        { }

        public ImportVehiclesForSiteResult(int siteId, string siteName, int processedVehiclesCount, int processedNewVehiclesCount, int processedUsedVehiclesCount, string message = null)
            : this(ImportStatusEnum.Success, siteId, siteName, message)
        {
            SiteId = siteId;
            SiteName = siteName;
            ProcessedVehiclesCount = processedVehiclesCount;
            ProcessedNewVehiclesCount = processedNewVehiclesCount;
            ProcessedUsedVehiclesCount = processedUsedVehiclesCount;
            Message = message;
        }

        #endregion
    }
}
