using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Entities.WidgetModule.Vehicles;
using QuantumLogic.Core.Domain.Services.Widget.Vehicles;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Vehicles;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Vehicles.Infos;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Vehicles;
using QuantumLogic.WebApi.DataModels.Responses;
using QuantumLogic.WebApi.DataModels.Responses.Widget.Vehicles;
using System;
using System.Collections.Generic;
using System.Globalization;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/vehicle")]
    public class VehicleController : EntityController<Vehicle, int, VehicleDto, VehicleFullDto>
    {
        #region Ctors

        public VehicleController(IQLUnitOfWorkManager uowManager, IVehicleDomainService domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        #region CRUD

        [HttpGet("{id:int}")]
        public Task<VehicleFullDto> GetAsync(int id)
        {
            return InnerGetAsync(id);
        }
        [Authorize]
        [HttpPost]
        public Task<VehicleFullDto> CreateAsync([FromBody]VehicleFullDto request)
        {
            return InnerCreateAsync(request);
        }
        [Authorize]
        [HttpPut]
        public Task<VehicleFullDto> UpdateAsync([FromBody]VehicleFullDto request)
        {
            return InnerUpdateAsync(request);
        }
        [Authorize]
        [HttpDelete("{id}")]
        public Task DeleteAsync(int id)
        {
            return InnerDeleteAsync(id);
        }

        #endregion

        #region Methods to operate with many entities

        [HttpPost("get-all/{page?}/{pageSize?}")]
        public Task<GetAllResponse<VehicleDto>> GetAllAsync([FromBody]VehicleGetAllRequest request, uint page = 0, uint pageSize = 0)
        {
            Expression<Func<Vehicle, bool>> filter = (entity) => (!request.SiteId.HasValue || request.SiteId.Value == entity.SiteId);
            return InnerGetAllAsync(filter, request.Sorting, page, pageSize);
        }

        #endregion

        #region Special methods

        [HttpGet("{siteId}/makes")]
        public async Task<VehicleMakesDto> GetVehicleMakes(int siteId)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                return new VehicleMakesDto(await ((IVehicleDomainService)DomainService).GetMakes(siteId));
            }
        }

        [HttpGet("{siteId}/models/{make?}")]
        public async Task<IEnumerable<VehicleModelInfoDto>> GetVehicleModels(int siteId, string make)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                return (await ((IVehicleDomainService)DomainService)
                    .GetModels(siteId, make))
                    .Select(vehicleModelInfo => new VehicleModelInfoDto(vehicleModelInfo));
            }
        }

        [HttpGet("{siteId}/years/{make?}/{model?}")]
        public async Task<IEnumerable<VehicleYearInfoDto>> GetVehicleYears(int siteId, string make, string model)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                return (await ((IVehicleDomainService)DomainService)
                    .GetYears(siteId, make, model))
                    .Select(vehicleYearInfo => new VehicleYearInfoDto(vehicleYearInfo))
                    .OrderByDescending(r => r.Name);
            }
        }

        [HttpGet("{siteId}/images/{make?}/{model?}/{year?}")]
        public Task<GetAllResponse<VehicleDto>> GetVehicles(int siteId, string make, string model, int? year)
        {
            Expression<Func<Vehicle, bool>> filter = (entity) => 
                entity.SiteId == siteId &&
                (String.IsNullOrEmpty(make) || entity.Make == make) &&
                (String.IsNullOrEmpty(model) || entity.Model == model) &&
                (!year.HasValue || entity.Year == year);
            return InnerGetAllAsync(filter, "Title asc", 0, Int32.MaxValue);
        }

        [HttpGet("{siteId}/search/{filter?}")]
        public Task<GetAllResponse<VehicleDto>> GetVehicles(int siteId, string filter)
        {
            Expression<Func<Vehicle, bool>> expression = (entity) =>
                entity.SiteId == siteId &&
                (
                    String.IsNullOrEmpty(filter) ||
                    (
                        CultureInfo.InvariantCulture.CompareInfo.IndexOf(filter, entity.Make, CompareOptions.IgnoreCase) >= 0 ||
                        CultureInfo.InvariantCulture.CompareInfo.IndexOf(entity.Make, filter, CompareOptions.IgnoreCase) >= 0
                    ) ||
                    (
                        CultureInfo.InvariantCulture.CompareInfo.IndexOf(filter, entity.Title, CompareOptions.IgnoreCase) >= 0 ||
                        CultureInfo.InvariantCulture.CompareInfo.IndexOf(entity.Title, filter, CompareOptions.IgnoreCase) >= 0
                    ) ||
                    (
                        CultureInfo.InvariantCulture.CompareInfo.IndexOf(filter, entity.Model, CompareOptions.IgnoreCase) >= 0 ||
                        CultureInfo.InvariantCulture.CompareInfo.IndexOf(entity.Model, filter, CompareOptions.IgnoreCase) >= 0
                    ) ||
                    (
#warning Bad performance!
                        CultureInfo.InvariantCulture.CompareInfo.IndexOf(filter, entity.Year.ToString(), CompareOptions.IgnoreCase) >= 0 ||
                        CultureInfo.InvariantCulture.CompareInfo.IndexOf(entity.Year.ToString(), filter, CompareOptions.IgnoreCase) >= 0
                    )
                );
            return InnerGetAllAsync(expression, "Title asc", 0, Int32.MaxValue);
        }

        #endregion
    }
}
