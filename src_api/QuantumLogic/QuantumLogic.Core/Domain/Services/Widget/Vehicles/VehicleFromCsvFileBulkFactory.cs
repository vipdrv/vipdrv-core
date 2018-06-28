using Csv;
using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Shared.Factories;
using QuantumLogic.Core.Shared.Providers;
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles
{
    public class VehicleFromCsvFileBulkFactory : IFactory<IEnumerable<Vehicle>, VehicleFromFileImportSettings>
    {
        #region Injected dependencies

        protected readonly IProvider<IDictionary<string, IEnumerable<string>>> VehicleImportFromCsvFilePossibleHeadersProvider;
        protected readonly VehicleFromCsvLineFactory VehicleFactory;

        #endregion

        #region Ctors

        public VehicleFromCsvFileBulkFactory(
            IProvider<IDictionary<string, IEnumerable<string>>> vehicleImportFromCsvFilePossibleHeadersProvider,
            VehicleFromCsvLineFactory vehicleFactory)
        {
            VehicleImportFromCsvFilePossibleHeadersProvider = vehicleImportFromCsvFilePossibleHeadersProvider;
            VehicleFactory = vehicleFactory;
        }

        #endregion

        public IEnumerable<Vehicle> Create(VehicleFromFileImportSettings settings)
        {
            IEnumerable<Vehicle> data;
            IEnumerable<ICsvLine> csvLines = CsvReader.ReadFromText(File.ReadAllText(settings.FilePath));
            IEnumerator<ICsvLine> csvLinesEnumerator = csvLines.GetEnumerator();
            if (csvLinesEnumerator.MoveNext())
            {
                IEnumerable<string> headers = csvLinesEnumerator.Current.Headers;
                IDictionary<string, string> mapping = VehicleImportFromCsvFilePossibleHeadersProvider
                    .Provide()
                    .Select(
                        mappingItem => new KeyValuePair<string, string>(
                            mappingItem.Key,
                            mappingItem.Value.Intersect(headers).FirstOrDefault()))
                    .Where(r => !String.IsNullOrWhiteSpace(r.Value))
                    .ToDictionary(r => r.Key, r => r.Value);
                data = csvLines.Select(csvLine => VehicleFactory.Create(new VehicleInfoFromCsvFile(settings.SiteId, csvLine, mapping)));
            }
            else
            {
                data = Enumerable.Empty<Vehicle>();
            }
            return data;
        }
    }
}
