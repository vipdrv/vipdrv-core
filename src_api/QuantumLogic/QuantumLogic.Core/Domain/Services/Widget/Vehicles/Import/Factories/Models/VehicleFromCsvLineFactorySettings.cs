using Csv;
using System.Collections.Generic;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories.Models
{
    public class VehicleFromCsvLineFactorySettings
    {
        public int SiteId { get; private set; }
        public ICsvLine CsvLine { get; private set; }
        public IDictionary<string, string> PropertyMapping { get; private set; }

        #region Ctors

        public VehicleFromCsvLineFactorySettings(int siteId, ICsvLine csvLine, IDictionary<string, string> propertyMapping)
        {
            SiteId = siteId;
            CsvLine = csvLine;
            PropertyMapping = propertyMapping;
        }

        #endregion
    }
}
