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
                try
                {
                    vehicle.Stock = line["Stock"];
                }
                catch (Exception e)
                {
                    try
                    {
                        vehicle.Stock = line["Stock #"];
                    }
                    catch (Exception e1)
                    {
                        try
                        {
                            vehicle.Stock = line["StockNumber"];
                        }
                        catch (Exception e2)
                        {
                            throw new Exception("Feed file parse Error: Stock");
                        }
                    }
                }

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
                try
                {
                    string usedNew = line["New/Used"];
                    if (usedNew == "N")
                    {
                        vehicle.Condition = VehicleConditions.New;
                    }
                    else if (usedNew == "U")
                    {
                        vehicle.Condition = VehicleConditions.Used;
                    }
                }
                catch (Exception e)
                {
                    try
                    {
                        string usedNew = line["UsedNew"];
                        if (usedNew == "N")
                        {
                            vehicle.Condition = VehicleConditions.New;
                        }
                        else if (usedNew == "U")
                        {
                            vehicle.Condition = VehicleConditions.Used;
                        }
                    }
                    catch (Exception e1)
                    {
                        try
                        {
                            string usedNew = line["Type"];
                            if (usedNew == "New")
                            {
                                vehicle.Condition = VehicleConditions.New;
                            }
                            else if (usedNew == "Used")
                            {
                                vehicle.Condition = VehicleConditions.Used;
                            }
                        }
                        catch (Exception e2)
                        {
                            throw new Exception("Feed file parse Error: Condition");
                        }
                    }
                }

                // ImageUrl
                try
                {
                    string imageUrLs = line["Photo Url List"];
                    if (!String.IsNullOrWhiteSpace(imageUrLs))
                    {
                        string firstImageUrl = imageUrLs.Split('|')[0];
                        vehicle.ImageUrl = firstImageUrl;
                    }
                }
                catch (Exception e)
                {
                    try
                    {
                        string imageUrLs = line["ImageURLs"];
                        if (!String.IsNullOrWhiteSpace(imageUrLs))
                        {
                            string firstImageUrl = imageUrLs.Split(',')[0];
                            vehicle.ImageUrl = firstImageUrl;
                        }
                    }
                    catch (Exception e1)
                    {
                        try
                        {
                            string imageUrLs = line["ImageList"];
                            if (!String.IsNullOrWhiteSpace(imageUrLs))
                            {
                                string firstImageUrl = imageUrLs.Split(',')[0];
                                vehicle.ImageUrl = firstImageUrl;
                            }
                        }
                        catch (Exception e2)
                        {
                            throw new Exception("Feed file parse Error: Image Url");
                        }
                    }
                }

                // Title
                vehicle.Title = ComposeVehicle(vehicle.Condition, vehicle.Make, vehicle.Model, vehicle.Year);
                vehiclesList.Add(vehicle);
                Console.WriteLine(vehicle.Title);
            }

            return vehiclesList;
        }
    }
}
