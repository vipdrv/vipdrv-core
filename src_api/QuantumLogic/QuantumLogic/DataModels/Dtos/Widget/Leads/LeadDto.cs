using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads
{
    public class LeadDto : EntityDto<Lead, int>
    {
        public int SiteId { get; set; }
        public int ExpertId { get; set; }
        public int BeverageId { get; set; }
        public int RouteId { get; set; }
        public DateTime Recieved { get; set; }
        public int UserName { get; set; }
        public string UserPhone { get; set; }
        public string UserEmail { get; set; }

        #region Mapping

        public override void MapFromEntity(Lead entity)
        {
            base.MapFromEntity(entity);
            SiteId = entity.SiteId;
            ExpertId = entity.ExpertId;
            BeverageId = entity.BeverageId;
            RouteId = entity.RouteId;
            Recieved = entity.Recieved;
            UserName = entity.UserName;
            UserPhone = entity.UserPhone;
            UserEmail = entity.UserEmail;
        }
        public override Lead MapToEntity()
        {
            Lead entity = base.MapToEntity();
            entity.SiteId = SiteId;
            entity.ExpertId = ExpertId;
            entity.BeverageId = BeverageId;
            entity.RouteId = RouteId;
            entity.UserName = UserName;
            entity.UserPhone = UserPhone;
            entity.UserEmail = UserEmail;
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
