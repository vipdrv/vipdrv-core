using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles;
using QuantumLogic.Core.Shared.Factories;
using System;
using System.Collections.Generic;
using Xunit;

namespace QuantumLogic.xUnitTests.Core.Utils.Import
{
    public class VehicleImportTests
    {
        [Fact]
        public void BulkFactory_ShouldWorkVehicleFromCsvFileBulkFactory()
        {
            try
            {
                IFactory<IEnumerable<Vehicle>, VehicleFromFileImportSettings> vehicleBulkFactory = new VehicleFromCsvFileBulkFactory(
                    new VehicleImportFromCsvFilePossibleHeadersProvider(),
                    new VehicleFromCsvLineFactory());
                IEnumerable<Vehicle> vehicles = vehicleBulkFactory.Create(new VehicleFromFileImportSettings(1, @"C:\Temp\test.csv"));
                Assert.True(true);
            }
            catch (Exception ex)
            {
                Assert.True(false, ex.Message);
            }
        }
    }
}
