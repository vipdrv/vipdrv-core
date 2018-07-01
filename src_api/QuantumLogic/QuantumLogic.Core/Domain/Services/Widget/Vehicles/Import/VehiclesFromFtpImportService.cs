using FluentFTP;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories.Models;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Models;
using QuantumLogic.Core.Shared.Factories;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import
{
    public class VehiclesFromFtpImportService
    {
        #region Injected dependencies

        protected readonly IVehicleRepository VehicleRepository;
        protected readonly IInstantFactory<IFtpClient> FtpClientFactory;
        protected readonly IFactory<IEnumerable<Vehicle>, VehicleFromCsvFileBulkFactorySettings> VehicleBulkFactory;

        #endregion

        #region Ctors

        public VehiclesFromFtpImportService(
            IVehicleRepository vehicleRepository,
            IInstantFactory<IFtpClient> ftpClientFactory,
            IFactory<IEnumerable<Vehicle>, VehicleFromCsvFileBulkFactorySettings> vehicleBulkFactory)
        {
            VehicleRepository = vehicleRepository;
            FtpClientFactory = ftpClientFactory;
            VehicleBulkFactory = vehicleBulkFactory;
        }

        #endregion

        public async Task<VehicleImportForSiteResult> ImportVehiclesForSite(Site site)
        {
            VehicleImportForSiteResult importResult;
            using (var ftpClient = FtpClientFactory.Create())
            {
                await ftpClient.ConnectAsync();
                if (ftpClient.DirectoryExists(site.ImportRelativeFtpPath))
                {
                    FtpListItem lastModifiedFileInfo = (await ftpClient.GetListingAsync(site.ImportRelativeFtpPath, FtpListOption.Auto))
                        .Where(r => r.Type == FtpFileSystemObjectType.File)
                        .OrderByDescending(r => r.Modified)
                        .FirstOrDefault();
                    if (lastModifiedFileInfo != null)
                    {
                        IEnumerable<Vehicle> vehicles;
                        using (var csvFileStream = new MemoryStream(await ftpClient.DownloadAsync(lastModifiedFileInfo.FullName)))
                        {
                            vehicles = VehicleBulkFactory.Create(new VehicleFromCsvFileBulkFactorySettings(site.Id, csvFileStream));
                        }
                        await VehicleRepository.RefreshEntitiesForSiteAsync(site.Id, vehicles);
                        importResult = new VehicleImportForSiteResult(site.Id, site.Name, vehicles.Count());
                    }
                    else
                    {
                        importResult = new VehicleImportForSiteResult(site.Id, site.Name, "No files in specified folder.");
                    }
                }
                else
                {
                    importResult = new VehicleImportForSiteResult(site.Id, site.Name, "The specified folder (via site's relative path) was not found.");
                }
            }
            return importResult;
        }
    }
}
