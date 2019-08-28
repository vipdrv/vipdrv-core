using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads
{
    public class LeadFullDto : LeadDto
    {
        public string CarImageUrl { get; set; }
        public int TimeZoneOffset { get; set; }

        #region Ctors

        public LeadFullDto()
            : base()
        {
        }

        public LeadFullDto(int id,
            int siteId,
            int? expertId,
            int? beverageId,
            int? routeId,
            string firstName,
            string secondName,
            string userPhone,
            string userEmail,
            string userComment,
            string carImageUrl,
            string carTitle,
            string carVin,
            string vdpUrl,
            DateTime? bookingDateTimeUtc,
            bool isNew,
            bool showLocationInfo,
            string locationType,
            string locationAddress
            )
            : base(
                  id: id,
                  siteId: siteId,
                  expertId: expertId,
                  beverageId: beverageId,
                  routeId: routeId,
                  firstName: firstName,
                  secondName: secondName,
                  userPhone: userPhone,
                  userEmail: userEmail,
                  userComment: userComment,
                  carTitle: carTitle,
                  carVin: carVin,
                  vdpUrl: vdpUrl,
                  bookingDateTimeUtc: bookingDateTimeUtc,
                  isNew: isNew,
                  showLocationInfo: showLocationInfo,
                  locationType: locationType,
                  locationAddress: locationAddress)
        {
            CarImageUrl = carImageUrl;
        }

        #endregion

        #region Mapping

        public override void MapFromEntity(Lead entity)
        {
            base.MapFromEntity(entity);
            CarImageUrl = entity.CarImageUrl;
        }

        public override Lead MapToEntity()
        {
            Lead entity = base.MapToEntity();
            entity.CarImageUrl = CarImageUrl;
            return entity;
        }

        #endregion
    }
}