using System;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.WidgetEvents
{
    public class WidgetEventDto : EntityDto<Core.Domain.Entities.WidgetModule.WidgetEvent, int>
    {
        public int SiteId { get; set; }

        public DateTime RecievedUtc { get; set; }
        public string EventTitle { get; set; }
        public string EventLevel { get; set; }
        public string EventDetails { get; set; }
        public bool IsResolved { get; set; }

        #region Mapping

        public override void MapFromEntity(Core.Domain.Entities.WidgetModule.WidgetEvent entity)
        {
            base.MapFromEntity(entity);
            SiteId = entity.SiteId;
            RecievedUtc = entity.RecievedUtc;
            EventTitle = entity.EventTitle;
            EventLevel = entity.EventLevel;
            EventDetails = entity.EventDetails;
            IsResolved = entity.IsResolved;
        }
        public override Core.Domain.Entities.WidgetModule.WidgetEvent MapToEntity()
        {
            Core.Domain.Entities.WidgetModule.WidgetEvent entity = base.MapToEntity();
            entity.SiteId = SiteId;
            entity.RecievedUtc = RecievedUtc;
            entity.EventTitle = EventTitle;
            entity.EventLevel = EventLevel;
            entity.EventDetails = EventDetails;
            entity.IsResolved = IsResolved;
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
