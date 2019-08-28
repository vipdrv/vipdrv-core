using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Utils.Vehicles;
using QuantumLogic.Core.Utils.Vehicles.Infos;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.xUnitTests.Core.Utils.Import.Mocks
{
    public class MockVehicleRepository : IVehicleRepository
    {
        private int _currentId = 0;
        private readonly HashSet<Vehicle> _vehicles = new HashSet<Vehicle>();

        public async Task RefreshEntitiesForSiteAsync(int siteId, IEnumerable<Vehicle> actualEntities)
        {
            _vehicles.RemoveWhere(r => r.SiteId == siteId);
            foreach (var vehicle in actualEntities)
            {
                await CreateAsync(vehicle);
            }
        }


        public bool OnSystemFilters { get => throw new NotImplementedException(); set => throw new NotImplementedException(); }

        public IQueryable<Vehicle> Query => throw new NotImplementedException();

        public Task CreateAsync(Vehicle entity)
        {
            entity.Id = _currentId++;
            _vehicles.Add(entity);
            return Task.CompletedTask;
        }

        public Task DeleteAsync(Vehicle entity)
        {
            _vehicles.Remove(entity);
            return Task.CompletedTask;
        }

        public Task DeleteRange(Func<IQueryable<Vehicle>, IQueryable<Vehicle>> queryBuilder)
        {
            throw new NotImplementedException();
        }

        public Task<Vehicle> FirstAsync(Expression<Func<Vehicle, bool>> filter, params Expression<Func<Vehicle, object>>[] includes)
        {
            throw new NotImplementedException();
        }

        public Task<Vehicle> FirstOrDefaultAsync(Expression<Func<Vehicle, bool>> filter, params Expression<Func<Vehicle, object>>[] includes)
        {
            throw new NotImplementedException();
        }

        public Task<IList<Vehicle>> GetAllAsync(Func<IQueryable<Vehicle>, IQueryable<Vehicle>> queryBuilder, params Expression<Func<Vehicle, object>>[] includes)
        {
            throw new NotImplementedException();
        }

        public Task<Vehicle> GetAsync(int id, params Expression<Func<Vehicle, object>>[] includes)
        {
            throw new NotImplementedException();
        }

        public Task<VehicleMakesModel> GetMakes(Expression<Func<Vehicle, bool>> predicate)
        {
            throw new NotImplementedException();
        }

        public Task<IEnumerable<VehicleModelInfo>> GetModels(Expression<Func<Vehicle, bool>> predicate)
        {
            throw new NotImplementedException();
        }

        public Task<int> GetTotalCountAsync(Func<IQueryable<Vehicle>, IQueryable<Vehicle>> queryBuilder)
        {
            throw new NotImplementedException();
        }

        public Task<IEnumerable<VehicleYearInfo>> GetYears(Expression<Func<Vehicle, bool>> predicate)
        {
            throw new NotImplementedException();
        }

        public Task<Vehicle> SingleAsync(Expression<Func<Vehicle, bool>> filter, params Expression<Func<Vehicle, object>>[] includes)
        {
            throw new NotImplementedException();
        }

        public Task<Vehicle> SingleOrDefaultAsync(Expression<Func<Vehicle, bool>> filter, params Expression<Func<Vehicle, object>>[] includes)
        {
            throw new NotImplementedException();
        }

        public Task UpdateAsync(Vehicle entity)
        {
            throw new NotImplementedException();
        }
    }
}
