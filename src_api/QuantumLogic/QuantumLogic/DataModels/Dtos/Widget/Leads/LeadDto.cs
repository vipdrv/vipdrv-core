using System;
using System.Globalization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads
{
    public class LeadDto : EntityDto<Lead, int>
    {
        #region Fields

        public int SiteId { get; set; }
        public int? ExpertId { get; set; }
        public int? BeverageId { get; set; }
        public int? RouteId { get; set; }
        public string RecievedUtc { get; set; }
        public string FirstName { get; set; }
        public string SecondName { get; set; }
        public string UserPhone { get; set; }
        public string UserEmail { get; set; }
        public string UserComment { get; set; }
        public string CarTitle { get; set; }
        public string CarVin { get; set; }
        public string VdpUrl { get; set; }
        public DateTime? BookingDateTimeUtc { get; set; }
        public bool IsNew { get; set; }
        public bool IsReachedByManager { get; set; }
        public bool ShowLocationInfo { get; set; }
        public string LocationType { get; set; }
        public string LocationAddress { get; set; }
        #endregion

        #region Relations

        public string SiteName { get; set; }
        public string ExpertName { get; set; }
        public string BeverageName { get; set; }
        public string RouteName { get; set; }

        #endregion

        #region Ctors

        public LeadDto() : base()
        {
        }

        public LeadDto(int id,
            int siteId,
            int? expertId,
            int? beverageId,
            int? routeId,
            string firstName,
            string secondName,
            string userPhone,
            string userEmail,
            string userComment,
            string carTitle,
            string carVin,
            string vdpUrl,
            DateTime? bookingDateTimeUtc,
            bool isNew,
            bool showLocationInfo,
            string locationType,
            string locationAddress) : this()
        {
            Id = id;
            SiteId = siteId;
            ExpertId = expertId;
            BeverageId = beverageId;
            RouteId = routeId;
            FirstName = firstName;
            SecondName = secondName;
            UserPhone = userPhone;
            UserEmail = userEmail;
            UserComment = userComment;
            CarTitle = carTitle;
            CarVin = carVin;
            VdpUrl = vdpUrl;
            BookingDateTimeUtc = bookingDateTimeUtc;
            IsNew = isNew;
            ShowLocationInfo = showLocationInfo;
            LocationType = locationType;
            LocationAddress = locationAddress;
        }

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
            UserComment = entity.UserComment;
            CarTitle = entity.CarTitle;
            CarVin = entity.CarVin;
            VdpUrl = entity.VdpUrl;
            BookingDateTimeUtc = entity.BookingDateTimeUtc;
            IsNew = entity.IsNew;
            IsReachedByManager = entity.IsReachedByManager;
            SiteName = entity.Site.Name;
            ExpertName = (entity.Expert != null) ? entity.Expert.Name : "Skipped by customer";
            BeverageName = (entity.Beverage != null) ? entity.Beverage.Name : "Skipped by customer";
            RouteName = (entity.Route != null) ? entity.Route.Name : "Skipped by customer";
            ShowLocationInfo = entity.ShowLocationInfo;
            LocationType = entity.LocationType;
            LocationAddress = entity.LocationAddress;
        }
        public override Lead MapToEntity()
        {
            Lead entity = base.MapToEntity();
            entity.RecievedUtc = DateTime.Now;
            entity.SiteId = SiteId;
            entity.ExpertId = ExpertId;
            entity.BeverageId = BeverageId;
            entity.RouteId = RouteId;
            entity.FirstName = FirstName;
            entity.SecondName = SecondName;
            entity.UserPhone = UserPhone;
            entity.UserEmail = UserEmail;
            entity.UserComment = UserComment;
            entity.CarTitle = CarTitle;
            entity.CarVin = CarVin;
            entity.VdpUrl = VdpUrl;
            entity.BookingDateTimeUtc = BookingDateTimeUtc;
            entity.IsNew = IsNew;
            entity.ShowLocationInfo = ShowLocationInfo;
            entity.LocationType = LocationType;
            entity.LocationAddress = LocationAddress;

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
