using FluentFTP;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Enums;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories.Models;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Models;
using QuantumLogic.Core.Shared.Factories;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Threading;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import
{
    public class VehiclesFromFtpImportService : IVehiclesImportService
    {
        private static readonly SemaphoreSlim _semaphore = new SemaphoreSlim(1, 1);

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

        public async Task<IEnumerable<ImportVehiclesForSiteResult>> Import(params Site[] sites)
        {
            await _semaphore.WaitAsync();
            try
            {
                List<ImportVehiclesForSiteResult> importResults = new List<ImportVehiclesForSiteResult>(sites.Count());
                using (var ftpClient = FtpClientFactory.Create())
                {
                    await ftpClient.ConnectAsync();
                    foreach (var site in sites)
                    {
                        importResults.Add(await InternalImportForSiteAsync(site, ftpClient));
                    }
                }
                return importResults;
            }
            finally
            {
                _semaphore.Release();
            }
        }

        #region Helpers

        protected virtual async Task<ImportVehiclesForSiteResult> InternalImportForSiteAsync(Site site, IFtpClient ftpClient)
        {
            ImportVehiclesForSiteResult importResult;
            if (site.ImportRelativeFtpPath != null)
            {
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
                        importResult = new ImportVehiclesForSiteResult(site.Id, site.Name, vehicles.Count());
                    }
                    else
                    {
                        importResult = new ImportVehiclesForSiteResult(site.Id, site.Name, $"No files in specified folder ({site.ImportRelativeFtpPath}).", ImportStatusEnum.NotStarted);
                    }
                }
                else
                {
                    importResult = new ImportVehiclesForSiteResult(site.Id, site.Name, $"The specified folder ({site.ImportRelativeFtpPath}) was not found.", ImportStatusEnum.NotStarted);
                }
            }
            else
            {
                importResult = new ImportVehiclesForSiteResult(site.Id, site.Name, $"The specified folder is not defined.", ImportStatusEnum.NotStarted);
            }
            return importResult;
        }

        #endregion
    }
}
