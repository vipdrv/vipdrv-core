using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Vehicles;
using QuantumLogic.Core.Utils.Vehicles.Infos;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Repositories.WidgetModule
{
    public interface IVehicleRepository : IQLRepository<Vehicle, int>
    {
        Task<VehicleMakesModel> GetMakes(Expression<Func<Vehicle, bool>> predicate);
        Task<IEnumerable<VehicleModelInfo>> GetModels(Expression<Func<Vehicle, bool>> predicate);
        Task<IEnumerable<VehicleYearInfo>> GetYears(Expression<Func<Vehicle, bool>> predicate);
    }
}
