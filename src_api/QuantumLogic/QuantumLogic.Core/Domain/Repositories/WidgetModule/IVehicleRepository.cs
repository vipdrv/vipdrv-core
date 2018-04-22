using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.VehicleMakes;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Repositories.WidgetModule
{
    public interface IVehicleRepository : IQLRepository<Vehicle, int>
    {
        Task<VehicleMakesModel> GetMakes(Expression<Func<Vehicle, bool>> predicate);
        Task<IEnumerable<string>> GetModels(Expression<Func<Vehicle, bool>> predicate);
        Task<IEnumerable<int>> GetYears(Expression<Func<Vehicle, bool>> predicate);
    }
}
