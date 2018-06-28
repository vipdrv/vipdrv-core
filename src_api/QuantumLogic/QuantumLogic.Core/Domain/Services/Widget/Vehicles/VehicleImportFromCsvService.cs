using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Shared.Factories;
using QuantumLogic.Core.Utils.Import.DataModels;
using QuantumLogic.Core.Utils.Import.Entity;
using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles
{
    public class VehicleImportFromCsvService : IEntityImportService<Vehicle, int>
    {
        #region Injected dependencies

        protected readonly IFactory<IEnumerable<Vehicle>, VehicleFromFileImportSettings> VehicleBulkFactory;

        #endregion

        #region Ctors

        public VehicleImportFromCsvService(IFactory<IEnumerable<Vehicle>, VehicleFromFileImportSettings> vehicleBulkFactory)
        {
            VehicleBulkFactory = vehicleBulkFactory;
        }

        #endregion

        public Task<IImportResult<Vehicle>> ImportAsync(IImportSettings settings)
        {
            VehicleFromFileImportSettings stub = settings as VehicleFromFileImportSettings;
            if (stub != null)
            {
                return Task.FromResult(InternalImport(stub));
            }
            else
            {
                throw new ArgumentException($"{nameof(settings)}");
            }
        }
        
        #region Helpers

        protected virtual IImportResult<Vehicle> InternalImport(VehicleFromFileImportSettings settings)
        {
            Stopwatch stopWatch = new Stopwatch();
            stopWatch.Start();

            // TODO: move file from ftp to local storage
            IEnumerable<Vehicle> data = VehicleBulkFactory.Create(settings);

            stopWatch.Stop();
            return new VehicleImportResult(data, stopWatch.Elapsed);
        }

        #endregion
    }
}
