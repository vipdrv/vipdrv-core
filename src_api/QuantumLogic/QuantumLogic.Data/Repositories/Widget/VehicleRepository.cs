using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Utils.VehicleMakes;
using QuantumLogic.Data.EFContext;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Data.Repositories.Widget
{
    public class VehicleRepository : EFRepository<Vehicle, int>, IVehicleRepository
    {
        #region Ctors

        public VehicleRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public VehicleRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion

        public Task<VehicleMakesModel> GetMakes(Expression<Func<Vehicle, bool>> predicate)
        {
            Func<Vehicle, bool> isNew = (entity) => entity.Id == 0;
            List<Tuple<string, VehicleConditions>> data = ((QuantumLogicDbContext)DbContextManager.BuildOrCurrentContext(out bool createdNew))
               .Vehicles
               .Where(predicate)
               .Select(entity => new Tuple<string, VehicleConditions>(entity.Make, entity.Condition))
#warning distinct should be here but not supported yet
               //.Distinct(new MakesEqualityComparer())
               .ToList()
               .Distinct(new MakesEqualityComparer())
               .ToList();

            if (createdNew)
            {
                DbContextManager.DisposeContext();
            }

            return Task.FromResult(new VehicleMakesModel(
                data.Where(r => r.Item2 == VehicleConditions.New).Select(r => r.Item1).OrderBy(r => r),
                data.Where(r => r.Item2 == VehicleConditions.Used).Select(r => r.Item1).OrderBy(r => r)));
        }

        public Task<IEnumerable<string>> GetModels(Expression<Func<Vehicle, bool>> predicate)
        {
            IEnumerable<string> data = ((QuantumLogicDbContext)DbContextManager.BuildOrCurrentContext(out bool createdNew))
               .Vehicles
               .Where(predicate)
               .Select(entity => entity.Model)
               .Distinct()
               .ToList();

            if (createdNew)
            {
                DbContextManager.DisposeContext();
            }

            return Task.FromResult(data);
        }

        public Task<IEnumerable<int>> GetYears(Expression<Func<Vehicle, bool>> predicate)
        {
            IEnumerable<int> data = ((QuantumLogicDbContext)DbContextManager.BuildOrCurrentContext(out bool createdNew))
               .Vehicles
               .Where(predicate)
               .Select(entity => entity.Year)
               .Distinct()
               .ToList();

            if (createdNew)
            {
                DbContextManager.DisposeContext();
            }

            return Task.FromResult(data);
        }

        protected class MakesEqualityComparer : IEqualityComparer<Tuple<string, VehicleConditions>>
        {
            public bool Equals(Tuple<string, VehicleConditions> x, Tuple<string, VehicleConditions> y)
            {
                return x.Item1 == y.Item1 && x.Item2 == y.Item2;
            }

            public int GetHashCode(Tuple<string, VehicleConditions> obj)
            {
                return obj.Item1.GetHashCode() + obj.Item2.GetHashCode();
            }
        }
    }
}
