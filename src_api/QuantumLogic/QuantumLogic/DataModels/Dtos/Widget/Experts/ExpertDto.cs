using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Experts
{
    public class ExpertDto : EntityDto<Expert, int>, IPassivable, IOrderable
    {
        public int SiteId { get; set; }
        public string Name { get; set; }
        public string Description { get; set; }
        public int Order { get; set; }
        public bool IsActive { get; set; }
        public string PhotoUrl { get; set; }
        public string FacebookUrl { get; set; }
        public string LinkedinUrl { get; set; }

        #region Mapping

        public override void MapFromEntity(Expert entity)
        {
            base.MapFromEntity(entity);
            SiteId = entity.SiteId;
            Name = entity.Name;
            Description = entity.Description;
            Order = entity.Order;
            IsActive = entity.IsActive;
            PhotoUrl = entity.PhotoUrl;
            FacebookUrl = entity.FacebookUrl;
            LinkedinUrl = entity.LinkedinUrl;
        }
        public override Expert MapToEntity()
        {
            Expert entity = base.MapToEntity();
            entity.SiteId = SiteId;
            entity.Name = Name;
            entity.Description = Description;
            entity.Order = Order;
            entity.IsActive = IsActive;
            entity.PhotoUrl = PhotoUrl;
            entity.FacebookUrl = FacebookUrl;
            entity.LinkedinUrl = LinkedinUrl;
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
