using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Models;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Sites.Import
{
    public class ImportVehiclesForSiteResultDto
    {
        public string Status { get; private set; }
        public string Message { get; private set; }
        public int SiteId { get; private set; }
        public string SiteName { get; private set; }
        public int ProcessedVehiclesCount { get; private set; }

        #region Ctors

        public ImportVehiclesForSiteResultDto(ImportVehiclesForSiteResult entity)
        {
            Status = entity.Status.ToString();
            Message = entity.Message;
            SiteId = entity.SiteId;
            SiteName = entity.SiteName;
            ProcessedVehiclesCount = entity.ProcessedVehiclesCount;
        }

        #endregion
    }
}
