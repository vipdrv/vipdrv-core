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
            List<Tuple<string, VehicleConditions>> dataAll = ((QuantumLogicDbContext)DbContextManager.BuildOrCurrentContext(out bool createdNew))
               .Vehicles
               .Where(predicate)
               .Select(entity => new Tuple<string, VehicleConditions>(entity.Make, entity.Condition))
#warning distinct should be here but not supported yet
               //.Distinct(new MakesEqualityComparer())
               .ToList();
            List<Tuple<string, VehicleConditions, int>> data = new List<Tuple<string, VehicleConditions, int>>();
            foreach (var make in dataAll.Distinct(new MakesEqualityComparer()))
            {
                data.Add(new Tuple<string, VehicleConditions, int>(
                        make.Item1,
                        make.Item2,
                        dataAll.Where(r => r.Item1 == make.Item1 && r.Item2 == make.Item2).Count()));

            }
            if (createdNew)
            {
                DbContextManager.DisposeContext();
            }

            return Task.FromResult(new VehicleMakesModel(
                data.Where(r => r.Item2 == VehicleConditions.New).OrderBy(r => r.Item3).Select(r => r.Item1),
                data.Where(r => r.Item2 == VehicleConditions.Used).OrderBy(r => r.Item3).Select(r => r.Item1)));
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
