using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Steps
{
    public class StepDto : EntityDto<Step, int>, IPassivable, IOrderable
    {
        public int SiteId { get; set; }
        public string Descriptor { get; set; }
        public string Name { get; set; }

        public int Order { get; set; }
        public bool IsActive { get; set; }

        public string SiteName { get; set; }

        #region Mapping

        public override void MapFromEntity(Step entity)
        {
            base.MapFromEntity(entity);
            Descriptor = entity.Descriptor;
            SiteId = entity.SiteId;
            Name = entity.Name;
            Order = entity.Order;
            IsActive = entity.IsActive;
            SiteName = entity.Site.Name;
        }
        public override Step MapToEntity()
        {
            Step entity = base.MapToEntity();
            entity.Descriptor = Descriptor;
            entity.SiteId = SiteId;
            entity.Name = Name;
            entity.Order = Order;
            entity.IsActive = IsActive;
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
