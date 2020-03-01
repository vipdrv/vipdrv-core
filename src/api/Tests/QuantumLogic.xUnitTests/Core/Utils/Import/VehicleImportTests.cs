using QuantumLogic.Core.Configurations;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Enums;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories.Models;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Models;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Providers;
using QuantumLogic.Core.Extensions;
using QuantumLogic.Core.Shared.Factories;
using QuantumLogic.xUnitTests.Core.Utils.Import.Mocks;
using QuantumLogic.xUnitTests.Mocks.Configurations.Options;
using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.Threading.Tasks;
using Xunit;

namespace QuantumLogic.xUnitTests.Core.Utils.Import
{
    public class VehicleImportTests
    {
        [Fact]
        public void VehicleFromCsvFileBulkFactory_ShouldNotThrowEx_Performance()
        {
            string filePath = @"C:\Temp\toyotaofsarasota.csv";
            int expectedSpeed = 150;
            try
            {
                IEnumerable<Vehicle> vehicles;
                Stopwatch stopWatch = new Stopwatch();
                IFactory<IEnumerable<Vehicle>, VehicleFromCsvFileBulkFactorySettings> vehicleBulkFactory = new VehicleFromCsvFileBulkFactory(
                    new VehicleImportFromCsvFilePossibleHeadersProvider(),
                    new VehicleFromCsvLineFactory());
                using (var csvFileStream = new MemoryStream(File.ReadAllBytes(filePath)))
                {
                    stopWatch.Start();
                    vehicles = vehicleBulkFactory.Create(new VehicleFromCsvFileBulkFactorySettings(1, csvFileStream)).ToList();
                    stopWatch.Stop();
                }
                int entitiesCount = vehicles.Count();
                int speed = (int)(entitiesCount / stopWatch.Elapsed.TotalSeconds);
                bool passed = speed >= expectedSpeed;
                string message = passed ?
                        $"Performance: {speed} entities/second. Elapsed {stopWatch.ElapsedMilliseconds} milliseconds to create {entitiesCount} vehicles." :
                        $"Performance failed! Expected: {expectedSpeed} entities/second. Actual: {speed} entities/second. Elapsed {stopWatch.ElapsedMilliseconds} milliseconds to create {entitiesCount} vehicles.";
                Assert.True(passed, message);
            }
            catch (Exception ex)
            {
                Assert.True(false, ex.Message);
            }
        }

        [Fact]
        public async Task VahicleImportService_ShouldNotThrowEx_Performance()
        {
            int expectedSpeed = 50;
            VehicleImportFtpServerConfiguration ftpConfiguration = new VehicleImportFtpServerConfiguration()
            {
                Host = "ftp://ftp.testdrive.pw",
                Username = "root",
                Password = "6gfb9P3xE2jAw7Sd",
            };
            Site mockSite = new Site()
            {
                Id = 1,
                Name = "MockSite",
                ImportRelativeFtpPath = @"/DealerFeed/28-TruckWorld"
            };
            try
            {
                Stopwatch stopWatch = new Stopwatch();

                ImportVehiclesForSiteResult importResult;
                VehiclesFromFtpImportService importService = new VehiclesFromFtpImportService(
                    new MockVehicleRepository(),
                    new VehicleImportFtpClientInstantFactory(
                        new MockOptions<VehicleImportFtpServerConfiguration>(ftpConfiguration)),
                    new VehicleFromCsvFileBulkFactory(
                        new VehicleImportFromCsvFilePossibleHeadersProvider(),
                        new VehicleFromCsvLineFactory()));

                stopWatch.Start();
                importResult = (await importService.Import(mockSite)).First();
                stopWatch.Stop();

                Assert.Equal(ImportStatusEnum.Success, importResult.Status);
                
                /// performance
                int speed = (int)(importResult.ProcessedVehiclesCount / stopWatch.Elapsed.TotalSeconds);
                bool passed = speed >= expectedSpeed;
                string message = passed ?
                        $"Performance: {speed} entities/second. Elapsed {stopWatch.ElapsedMilliseconds} milliseconds to create {importResult.ProcessedVehiclesCount} vehicles." :
                        $"Performance failed! Expected: {expectedSpeed} entities/second. Actual: {speed} entities/second. Elapsed {stopWatch.ElapsedMilliseconds} milliseconds to create {importResult.ProcessedVehiclesCount} vehicles.";
                Assert.True(passed, message);
            }
            catch (Exception ex)
            {
                Assert.True(false, ex.Message);
            }
        }
    }
}
