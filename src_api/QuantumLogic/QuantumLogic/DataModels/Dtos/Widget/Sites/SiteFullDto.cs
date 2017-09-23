using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System.Linq;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Sites
{
    public class SiteFullDto : SiteDto
    {
        public int ActiveExpertsAmount { get; set; }
        public int ActiveBeveragesAmount { get; set; }
        public int ActiveRoutesAmount { get; set; }

        public override void MapFromEntity(Site entity)
        {
            base.MapFromEntity(entity);
            ActiveExpertsAmount = entity.Experts.Where(r => r.IsActive).Count();
            ActiveBeveragesAmount = entity.Beverages.Where(r => r.IsActive).Count();
            ActiveRoutesAmount = entity.Routes.Where(r => r.IsActive).Count();
        }
    }
}
