using System.IO;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories.Models
{
    public class VehicleFromCsvFileBulkFactorySettings
    {
        public int SiteId { get; private set; }
        public Stream CsvFileStream { get; private set; }

        #region Ctors 

        public VehicleFromCsvFileBulkFactorySettings(int siteId, Stream csvFileStream)
        {
            SiteId = siteId;
            CsvFileStream = csvFileStream;
        }

        #endregion
    }
}
