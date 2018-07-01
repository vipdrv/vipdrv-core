using Csv;
using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories.Models;
using QuantumLogic.Core.Shared.Factories;
using QuantumLogic.Core.Shared.Providers;
using System;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories
{
    public class VehicleFromCsvFileBulkFactory : IFactory<IEnumerable<Vehicle>, VehicleFromCsvFileBulkFactorySettings>
    {
        #region Injected dependencies

        protected readonly IProvider<IDictionary<string, IEnumerable<string>>> VehicleImportFromCsvFilePossibleHeadersProvider;
        protected readonly IFactory<Vehicle, VehicleFromCsvLineFactorySettings> VehicleFactory;

        #endregion

        #region Ctors

        public VehicleFromCsvFileBulkFactory(
            IProvider<IDictionary<string, IEnumerable<string>>> vehicleImportFromCsvFilePossibleHeadersProvider,
            IFactory<Vehicle, VehicleFromCsvLineFactorySettings> vehicleFactory)
        {
            VehicleImportFromCsvFilePossibleHeadersProvider = vehicleImportFromCsvFilePossibleHeadersProvider;
            VehicleFactory = vehicleFactory;
        }

        #endregion

        public IEnumerable<Vehicle> Create(VehicleFromCsvFileBulkFactorySettings settings)
        {
            IEnumerable<Vehicle> data;
            IList<ICsvLine> csvLines = CsvReader.ReadFromStream(settings.CsvFileStream).ToList();
            if (csvLines.Count > 0)
            {
                IEnumerable<string> headers = csvLines.First().Headers;
                IDictionary<string, string> mapping = VehicleImportFromCsvFilePossibleHeadersProvider
                    .Provide()
                    .Select(
                        mappingItem => new KeyValuePair<string, string>(
                            mappingItem.Key,
                            mappingItem.Value.Intersect(headers).FirstOrDefault()))
                    .Where(r => !String.IsNullOrWhiteSpace(r.Value))
                    .ToDictionary(r => r.Key, r => r.Value);
                data = csvLines.Select(csvLine => VehicleFactory.Create(new VehicleFromCsvLineFactorySettings(settings.SiteId, csvLine, mapping)));
            }
            else
            {
                data = Enumerable.Empty<Vehicle>();
            }
            return data;
        }
    }
}
