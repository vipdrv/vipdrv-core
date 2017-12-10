using System;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads
{
    public class LeadFullDto : LeadDto
    {
        #region Ctors

        public LeadFullDto() 
            : base()
        { }

        public LeadFullDto(
            int id,
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
            DateTime bookingDateTimeUtc)
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
                  carImageUrl,
                  carTitle,
                  carVin,
                  bookingDateTimeUtc)
        { }

        #endregion
    }
}
