using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Shared.Providers;
using System.Collections.Generic;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles
{
    public class VehicleImportFromCsvFilePossibleHeadersProvider : IProvider<IDictionary<string, IEnumerable<string>>>
    {
        private readonly IDictionary<string, IEnumerable<string>> _possibleHeadersMapping;

        #region Ctors

        public VehicleImportFromCsvFilePossibleHeadersProvider()
        {
            IDictionary<string, IEnumerable<string>> mapping = new Dictionary<string, IEnumerable<string>>();
            mapping.Add(new KeyValuePair<string, IEnumerable<string>>(nameof(Vehicle.VIN), new List<string>() { "VIN" }));
            mapping.Add(new KeyValuePair<string, IEnumerable<string>>(nameof(Vehicle.Stock), new List<string>() { "Stock", "Stock #", "StockNumber" }));
            mapping.Add(new KeyValuePair<string, IEnumerable<string>>(nameof(Vehicle.Year), new List<string>() { "Year" }));
            mapping.Add(new KeyValuePair<string, IEnumerable<string>>(nameof(Vehicle.Make), new List<string>() { "Make" }));
            mapping.Add(new KeyValuePair<string, IEnumerable<string>>(nameof(Vehicle.Model), new List<string>() { "Model" }));
            mapping.Add(new KeyValuePair<string, IEnumerable<string>>(nameof(Vehicle.Condition), new List<string>() { "New/Used", "UsedNew", "Type" }));
            mapping.Add(new KeyValuePair<string, IEnumerable<string>>(nameof(Vehicle.ImageUrl), new List<string>() { "Photo Url List", "ImageURLs", "ImageList" }));
            _possibleHeadersMapping = mapping;
        }

        #endregion

        public IDictionary<string, IEnumerable<string>> Provide()
        {
            return _possibleHeadersMapping;
        }
    }
}
