using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.VehicleMakes;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles
{
    public interface IVehicleDomainService : IEntityDomainService<Vehicle, int>
    {
        Task<VehicleMakesModel> GetMakes(int siteId);
        Task<IEnumerable<string>> GetModels(int siteId, string make);
        Task<IEnumerable<int>> GetYears(int siteId, string make, string model);
    }
}
