using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Beverages
{
    public class BeverageDto : EntityDto<Beverage, int>, IPassivable, IOrderable
    {
        public int SiteId { get; set; }
        public string Name { get; set; }
        public string Description { get; set; }
        public int Order { get; set; }
        public bool IsActive { get; set; }
        public string PhotoUrl { get; set; }

        #region Mapping

        public override void MapFromEntity(Beverage entity)
        {
            base.MapFromEntity(entity);
            SiteId = entity.SiteId;
            Name = entity.Name;
            Description = entity.Description;
            Order = entity.Order;
            IsActive = entity.IsActive;
            PhotoUrl = entity.PhotoUrl;
        }
        public override Beverage MapToEntity()
        {
            Beverage entity = base.MapToEntity();
            entity.SiteId = SiteId;
            entity.Name = Name;
            entity.Description = Description;
            entity.Order = Order;
            entity.IsActive = IsActive;
            entity.PhotoUrl = PhotoUrl;
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
