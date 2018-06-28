using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Shared.Factories;
using System;
using System.Collections.Generic;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles
{
    public class VehicleFromCsvLineFactory : IFactory<Vehicle, VehicleInfoFromCsvFile>
    { 
        #region Settings

        private readonly IEnumerable<char> _imageUrlSeparators = new List<char>() { '|', '.', ',' };

        #endregion

        public Vehicle Create(VehicleInfoFromCsvFile settings)
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
            if (!String.IsNullOrWhiteSpace(imageUrlAsStringValue))
            {
                foreach (char separator in _imageUrlSeparators)
                {
                    if (imageUrlAsStringValue.IndexOf(separator) > -1)
                    {
                        return imageUrlAsStringValue.Split(separator)[0];
                    }
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
