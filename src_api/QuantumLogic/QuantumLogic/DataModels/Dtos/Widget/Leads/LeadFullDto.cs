using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads
{
    public class LeadFullDto : LeadDto
    {
        public string CarImageUrl { get; set; }

        #region Ctors

        public LeadFullDto()
            : base()
        { }

        public LeadFullDto(int id,
            int siteId,
            int? expertId,
            int? beverageId,
            int? routeId,
            string firstName,
            string secondName,
            string userPhone,
            string userEmail,
            string carImageUrl,
            string carTitle,
            string carVin,
            string bookingDateTimeUtc)
            : base(
                  id,
                  siteId,
                  expertId,
                  beverageId,
                  routeId,
                  firstName,
                  secondName,
                  userPhone,
                  userEmail,
                  carTitle,
                  carVin,
                  bookingDateTimeUtc)
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
