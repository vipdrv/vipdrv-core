using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads
{
    public class LeadDto : EntityDto<Lead, int>
    {
        public int SiteId { get; set; }
        public int ExpertId { get; set; }
        public int? BeverageId { get; set; }
        public int RouteId { get; set; }
        public string RecievedUtc { get; set; }
        public string FirstName { get; set; }
        public string SecondName { get; set; }
        public string UserPhone { get; set; }
        public string UserEmail { get; set; }

        #region Relations

        public string SiteName { get; set; }
        public string ExpertName { get; set; }
        public string BeverageName { get; set; }
        public string RouteName { get; set; }

        #endregion

        #region Mapping

        public override void MapFromEntity(Lead entity)
        {
            base.MapFromEntity(entity);
            SiteId = entity.SiteId;
            ExpertId = entity.ExpertId;
            BeverageId = entity.BeverageId;
            RouteId = entity.RouteId;
            RecievedUtc = entity.RecievedUtc.ToString(QuantumLogicConstants.OutputDateTimeFormat);
            FirstName = entity.FirstName;
            SecondName = entity.SecondName;
            UserPhone = entity.UserPhone;
            UserEmail = entity.UserEmail;
            SiteName = entity.Site.Name;
            ExpertName = entity.Expert.Name;
            BeverageName = entity.Beverage.Name;
            RouteName = entity.Route.Name;
        }
        public override Lead MapToEntity()
        {
            Lead entity = base.MapToEntity();
            entity.SiteId = SiteId;
            entity.ExpertId = ExpertId;
            entity.BeverageId = BeverageId;
            entity.RouteId = RouteId;
            entity.FirstName = FirstName;
            entity.SecondName = SecondName;
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
