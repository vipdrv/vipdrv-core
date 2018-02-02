using QuantumLogic.WebApi.DataModels.Dtos.Widget.Beverages;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Experts;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Routes;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Sites
{
    public class SiteAgregatedInfoDto
    {
        public SiteFullDto Site { get; set; }
        public IEnumerable<BeverageDto> Beverages { get; set; }
        public IEnumerable<ExpertDto> Experts { get; set; }
        public IEnumerable<RouteDto> Routes { get; set; }

        #region Ctors

        public SiteAgregatedInfoDto()
        { }

        public SiteAgregatedInfoDto(SiteFullDto siteDto, IEnumerable<BeverageDto> beverageDtos, IEnumerable<ExpertDto> expertDtos, IEnumerable<RouteDto> routeDtos)
        {
            Site = siteDto;
            Beverages = beverageDtos;
            Experts = expertDtos;
            Routes = routeDtos;
        }

        #endregion
    }
}
