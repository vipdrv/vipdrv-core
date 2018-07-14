using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories.Models;
using QuantumLogic.Core.Shared.Factories;
using System;
using System.Linq;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories
{
    public class VehicleFromCsvLineFactory : IFactory<Vehicle, VehicleFromCsvLineFactorySettings>
    {
        #region Settings

        private readonly string[] _imageUrlSeparators = new string[] { "|", ".", "," };
        private readonly string[] _availableImageUrlStarts = new string[] { "http://", "https://" };

        #endregion

        public Vehicle Create(VehicleFromCsvLineFactorySettings settings)
        {
            Vehicle vehicle = new Vehicle();
            vehicle.SiteId = settings.SiteId;
            vehicle.VIN = settings.CsvLine[settings.PropertyMapping[nameof(Vehicle.VIN)]];
            vehicle.Stock = settings.CsvLine[settings.PropertyMapping[nameof(Vehicle.Stock)]];
            vehicle.Year = Int32.Parse(settings.CsvLine[settings.PropertyMapping[nameof(Vehicle.Year)]]);
            vehicle.Make = settings.CsvLine[settings.PropertyMapping[nameof(Vehicle.Make)]];
            vehicle.Model = settings.CsvLine[settings.PropertyMapping[nameof(Vehicle.Model)]];
            vehicle.Condition = CreateCondition(settings.CsvLine[settings.PropertyMapping[nameof(Vehicle.Condition)]]);
            vehicle.ImageUrl = CreateImageUrl(settings.CsvLine[settings.PropertyMapping[nameof(Vehicle.ImageUrl)]]);
            vehicle.Title = CreateTitle(vehicle);
            return vehicle;
        }

        #region Helpers

        protected virtual VehicleConditions CreateCondition(string conditionAsString)
        {
            VehicleConditions condition;
            if (conditionAsString.StartsWith("N", StringComparison.OrdinalIgnoreCase))
            {
                condition = VehicleConditions.New;
            }
            else if (conditionAsString.StartsWith("U", StringComparison.OrdinalIgnoreCase))
            {
                condition = VehicleConditions.Used;
            }
            else
            {
                condition = VehicleConditions.Undefined;
            }
            return condition;
        }

        protected virtual string CreateImageUrl(string imageUrlAsStringValue)
        {
            string usedImageStart = _availableImageUrlStarts.FirstOrDefault(r => imageUrlAsStringValue.StartsWith(r));
            if (usedImageStart != null)
            {
                string[] splits = imageUrlAsStringValue.Split(_availableImageUrlStarts, StringSplitOptions.None);
                if (splits.Count() > 1)
                {
                    string firstImgUrl = splits[1];
                    if (_imageUrlSeparators.Any(urlSep => firstImgUrl.EndsWith(urlSep)))
                    {
                        firstImgUrl = firstImgUrl.Remove(firstImgUrl.Length - 1);
                    }
                    return $"{usedImageStart}{firstImgUrl}";
                }
            }
            return String.Empty;
        }

        protected virtual string CreateTitle(Vehicle vehicle)
        {
            string vehicleCondition = vehicle.Condition == VehicleConditions.Undefined ? String.Empty : vehicle.Condition.ToString();
            return $"{vehicleCondition} {vehicle.Make} {vehicle.Model} {vehicle.Year}";
        }

        #endregion
    }
}
