using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Models;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import
{
    public interface IVehiclesImportService
    {
        Task<IEnumerable<ImportVehiclesForSiteResult>> Import(params Site[] sites);
    }
}
