using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Vehicles;
using QuantumLogic.Core.Utils.Vehicles.Infos;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles
{
    public interface IVehicleDomainService : IEntityDomainService<Vehicle, int>
    {
        Task<VehicleMakesModel> GetMakes(int siteId);
        Task<IEnumerable<VehicleModelInfo>> GetModels(int siteId, string make);
        Task<IEnumerable<VehicleYearInfo>> GetYears(int siteId, string make, string model);
    }
}
