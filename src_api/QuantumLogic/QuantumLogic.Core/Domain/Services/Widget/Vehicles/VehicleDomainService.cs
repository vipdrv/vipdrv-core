using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Models;
using QuantumLogic.Core.Domain.Validation.Widget;
using QuantumLogic.Core.Utils.Vehicles;
using QuantumLogic.Core.Utils.Vehicles.Infos;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles
{
    public class VehicleDomainService : EntityDomainService<Vehicle, int>, IVehicleDomainService
    {
        #region Injected dependencies

        protected readonly VehiclesFromFtpImportService ImportService;
        protected readonly ISiteRepository SiteRepository;

        #endregion

        #region Ctors

        public VehicleDomainService(IVehicleRepository repository, IVehiclePolicy policy, IVehicleValidationService validationService, VehiclesFromFtpImportService importService, ISiteRepository siteRepository)
            : base(repository, policy, validationService)
        {
            ImportService = importService;
            SiteRepository = siteRepository;
        }

        #endregion

        public async Task<VehicleImportForSiteResult> ImportEntitiesForSiteAsync(int siteId)
        {
            Site site = await SiteRepository.GetAsync(siteId);
            ((IVehiclePolicy)Policy).PolicyImport(site);
            return await ImportService.ImportVehiclesForSite(site);
        }

        public Task<VehicleMakesModel> GetMakes(int siteId)
        {
            Expression<Func<Vehicle, bool>> filterExpression = ((entity) => entity.SiteId == siteId);
            Expression body = Expression.AndAlso(((IVehiclePolicy)Policy).GetRetrieveAllExpression().Body, filterExpression.Body);
            Expression<Func<Vehicle, bool>> lambda = Expression.Lambda<Func<Vehicle, bool>>(body, filterExpression.Parameters[0]);
            return ((IVehicleRepository)Repository).GetMakes(lambda);
        }
        public Task<IEnumerable<VehicleModelInfo>> GetModels(int siteId, string make)
        {
            Expression<Func<Vehicle, bool>> filterExpression = ((entity) => entity.SiteId == siteId && entity.Make == make);
            Expression body = Expression.AndAlso(((IVehiclePolicy)Policy).GetRetrieveAllExpression().Body, filterExpression.Body);
            Expression<Func<Vehicle, bool>> lambda = Expression.Lambda<Func<Vehicle, bool>>(body, filterExpression.Parameters[0]);
            return ((IVehicleRepository)Repository).GetModels(lambda);
        }
        public Task<IEnumerable<VehicleYearInfo>> GetYears(int siteId, string make, string model)
        {
            Expression<Func<Vehicle, bool>> filterExpression = ((entity) => entity.SiteId == siteId && entity.Make == make && entity.Model == model);
            Expression body = Expression.AndAlso(((IVehiclePolicy)Policy).GetRetrieveAllExpression().Body, filterExpression.Body);
            Expression<Func<Vehicle, bool>> lambda = Expression.Lambda<Func<Vehicle, bool>>(body, filterExpression.Parameters[0]);
            return ((IVehicleRepository)Repository).GetYears(lambda);
        }

        protected override Task CascadeDeleteActionAsync(Vehicle entity)
        {
            return Task.CompletedTask;
        }
        internal override IEnumerable<LoadEntityRelationAction<Vehicle>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Vehicle>>();
        }
        protected override Expression<Func<Vehicle, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Vehicle, object>>>()
            {
                //entity => entity.Site
            }
            .ToArray();
        }
        protected override Expression<Func<Vehicle, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<Vehicle, object>>>()
            {
                //entity => entity.Site
            }
            .ToArray();
        }
    }
}
