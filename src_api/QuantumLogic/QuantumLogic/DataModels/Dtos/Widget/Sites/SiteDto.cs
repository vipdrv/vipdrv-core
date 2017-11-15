using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System.Linq;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Sites
{
    public class SiteDto : EntityDto<Site, int>
    {
        public int UserId { get; set; }
        public string BeautyId { get; set; }
        public string Name { get; set; }
        public string Url { get; set; }
        public string Contacts { get; set; }
        public int LeadsAmount { get; set; }
        public int ExpertsAmount { get; set; }
        public int BeveragesAmount { get; set; }
        public int RoutesAmount { get; set; }

        public int ActiveExpertsAmount { get; set; }
        public int ActiveBeveragesAmount { get; set; }
        public int ActiveRoutesAmount { get; set; }

        #region Mapping

        public override void MapFromEntity(Site entity)
        {
            base.MapFromEntity(entity);
            UserId = entity.UserId;
            BeautyId = entity.BeautyId;
            Name = entity.Name;
            Url = entity.Url;
            Contacts = entity.Contacts;
            LeadsAmount = entity.Leads.Count;
            ExpertsAmount = entity.Experts.Count;
            BeveragesAmount = entity.Beverages.Count;
            RoutesAmount = entity.Routes.Count;
            ActiveExpertsAmount = entity.Experts.Where(r => r.IsActive).Count();
            ActiveBeveragesAmount = entity.Beverages.Where(r => r.IsActive).Count();
            ActiveRoutesAmount = entity.Routes.Where(r => r.IsActive).Count();
        }
        public override Site MapToEntity()
        {
            Site entity = base.MapToEntity();
            entity.UserId = UserId;
            entity.BeautyId = BeautyId;
            entity.Name = Name;
            entity.Url = Url;
            entity.Contacts = Contacts;
            return entity;
        }

        #endregion

        #region Normalization

        public override void NormalizeAsRequest()
        { }
        public override void NormalizeAsResponse()
        { }

        #endregion
    }
}
