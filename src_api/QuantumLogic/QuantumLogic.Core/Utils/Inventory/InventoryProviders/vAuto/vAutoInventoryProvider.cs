using System;
using System.Collections.Generic;
using Csv;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Utils.Inventory.InventoryProviders.vAuto
{
    public class VAutoInventoryProvider : BaseInventoryProvider, IInventoryProvider
    {
        public IList<Vehicle> ParseVehiclesFromCsv(string fileLocation, int siteId)
        {
            IList<Vehicle> vehiclesList = new List<Vehicle>();

            foreach (var line in CsvReader.ReadFromText(fileLocation))
            {
                Vehicle vehicle = new Vehicle();
                vehicle.SiteId = siteId;
                // VIN
                vehicle.VIN = line["VIN"];

                // Stock
                vehicle.Stock = line["Stock #"];

                // Year
                string stringYear = line["Year"];
                int intYear;
                if (Int32.TryParse(stringYear, out intYear))
                {
                    vehicle.Year = intYear;
                }

                // Make
                vehicle.Make = line["Make"];

                // Model
                vehicle.Model = line["Model"];

                // Condition
                string usedNew = line["New/Used"];
                if (usedNew == "N")
                {
                    vehicle.Condition = VehicleConditions.New;
                }
                else if (usedNew == "U")
                {
                    vehicle.Condition = VehicleConditions.Used;
                }

                // ImageUrl
                string imageUrLs = line["Photo Url List"];
                if (!String.IsNullOrWhiteSpace(imageUrLs))
                {
                    string firstImageUrl = imageUrLs.Split('|')[0];
                    vehicle.ImageUrl = firstImageUrl;
                }

                // Title
                vehicle.Title = ComposeVehicle(vehicle.Condition, vehicle.Make, vehicle.Model, vehicle.Year);

                vehiclesList.Add(vehicle);
            }

            return vehiclesList;
        }
    }
}
