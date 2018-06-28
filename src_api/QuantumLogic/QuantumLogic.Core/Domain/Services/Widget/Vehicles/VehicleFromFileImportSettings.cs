using QuantumLogic.Core.Utils.Import.DataModels;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles
{
    public class VehicleFromFileImportSettings : IImportSettings
    {
        public int SiteId { get; private set; }
        public string FilePath { get; private set; }

        #region Ctors

        public VehicleFromFileImportSettings(int siteId, string filePath)
        {
            SiteId = siteId;
            FilePath = filePath;
        }

        #endregion
    }
}
