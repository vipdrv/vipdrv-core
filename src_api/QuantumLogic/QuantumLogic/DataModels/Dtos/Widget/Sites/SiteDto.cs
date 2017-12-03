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
        public string ImageUrl { get; set; }
        public int LeadsAmount { get; set; }
        public int NewLeadsAmount { get; set; }

        #region Mapping

        public override void MapFromEntity(Site entity)
        {
            base.MapFromEntity(entity);
            UserId = entity.UserId;
            BeautyId = entity.BeautyId;
            Name = entity.Name;
            Url = entity.Url;
            Contacts = entity.Contacts;
            ImageUrl = entity.ImageUrl;
            LeadsAmount = entity.Leads.Count;
            NewLeadsAmount = entity.Leads.Count(r => r.IsNew);
        }
        public override Site MapToEntity()
        {
            Site entity = base.MapToEntity();
            entity.UserId = UserId;
            entity.BeautyId = BeautyId;
            entity.Name = Name;
            entity.Url = Url;
            entity.Contacts = Contacts;
            entity.ImageUrl = ImageUrl;
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
