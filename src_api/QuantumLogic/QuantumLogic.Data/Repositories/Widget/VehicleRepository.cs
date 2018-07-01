using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Utils.Vehicles;
using QuantumLogic.Core.Utils.Vehicles.Infos;
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
        #region Injected dependencies

        protected readonly VehicleMakesImageManager VehicleMakesImageManager;

        #endregion

        #region Ctors

        public VehicleRepository(DbContextManager dbContextManager, VehicleMakesImageManager vehicleMakesImageManager)
            : base(dbContextManager)
        {
            VehicleMakesImageManager = vehicleMakesImageManager;
        }

        public VehicleRepository(DbContextManager dbContextManager, bool onSystemFilters, VehicleMakesImageManager vehicleMakesImageManager)
            : base(dbContextManager, onSystemFilters)
        {
            VehicleMakesImageManager = vehicleMakesImageManager;
        }

        #endregion

        public Task RefreshEntitiesForSiteAsync(int siteId, IEnumerable<Vehicle> actualEntities)
        {
            QuantumLogicDbContext context = ((QuantumLogicDbContext)DbContextManager.BuildOrCurrentContext(out bool createdNew));
            context.Vehicles.RemoveRange(context.Vehicles.Where(r => r.SiteId == siteId));
            return context.Vehicles.AddRangeAsync(actualEntities);
        }

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

            return Task.FromResult(
                new VehicleMakesModel(
                    data.Where(r => r.Item2 == VehicleConditions.New)
                        .Select(r => new VehicleMakeInfo(r.Item1, r.Item3, VehicleMakesImageManager.GetImageForMake(r.Item1))),
                    data.Where(r => r.Item2 == VehicleConditions.Used)
                        .Select(r => new VehicleMakeInfo(r.Item1, r.Item3, VehicleMakesImageManager.GetImageForMake(r.Item1)))));
        }

        public Task<IEnumerable<VehicleModelInfo>> GetModels(Expression<Func<Vehicle, bool>> predicate)
        {
            IEnumerable<VehicleModelInfo> data = ((QuantumLogicDbContext)DbContextManager.BuildOrCurrentContext(out bool createdNew))
               .Vehicles
               .Where(predicate)
               .OrderBy(r=>r.Id)
               .GroupBy(entity => entity.Model.ToUpperInvariant())
               .Select(grouping => new VehicleModelInfo(grouping.Last().Model, grouping.Count(), grouping.Last().ImageUrl))
               .ToList();

            if (createdNew)
            {
                DbContextManager.DisposeContext();
            }

            return Task.FromResult(data);
        }

        public Task<IEnumerable<VehicleYearInfo>> GetYears(Expression<Func<Vehicle, bool>> predicate)
        {
            IEnumerable<VehicleYearInfo> data = ((QuantumLogicDbContext)DbContextManager.BuildOrCurrentContext(out bool createdNew))
               .Vehicles
               .Where(predicate)
               .OrderBy(r => r.Id)
               .GroupBy(entity => entity.Year)
               .Select(grouping => new VehicleYearInfo(grouping.Last().Year, grouping.Count()))
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
