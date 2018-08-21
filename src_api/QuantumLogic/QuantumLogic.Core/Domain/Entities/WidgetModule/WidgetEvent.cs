using System;

namespace QuantumLogic.Core.Domain.Entities.WidgetModule
{
    public class WidgetEvent : Entity<int>, IValidable, IUpdatableFrom<WidgetEvent>
    {
        public int SiteId { get; set; }
        public Site Site { get; set; }

        public DateTime RecievedUtc { get; set; }
        public string EventTitle { get; set; }
        public string EventLevel { get; set; }
        public string EventDetails { get; set; }
        public bool IsResolved { get; set; }

        #region IValidable implementation

        public bool IsValid()
        {
            return InnerValidate(false);
        }
        public void Validate()
        {
            InnerValidate(true);
        }
        protected virtual bool InnerValidate(bool throwException)
        {
            return true;
        }

        #endregion

        #region IUpdatable implementation

        public void UpdateFrom(WidgetEvent actualEntity)
        {
            SiteId = actualEntity.SiteId;
            RecievedUtc = actualEntity.RecievedUtc;
            EventTitle = actualEntity.EventTitle;
            EventLevel = actualEntity.EventLevel;
            EventDetails = actualEntity.EventDetails;
            IsResolved = actualEntity.IsResolved;
        }

        #endregion
    }
}
