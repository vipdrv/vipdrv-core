using System;
using System.Collections.Generic;
using System.Globalization;
using System.Text;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Utils.Sms.Templates
{
    public class CompleteBookingSmsTemplate : ISmsTemplate
    {
        public string VehicleTitle { get; }
        public DateTime? BookingDateTimeUtc { get; }
        public int TimeZoneOffset { get; }
        public string ExpertName { get; }
        public string BeverageName { get; }
        public string RoadName { get; }
        public string DealerName { get; set; }
        public string DealerPhone { get; set; }

        public CompleteBookingSmsTemplate(
            string vehicleTitle,
            DateTime? bookingDateTimeUtc,
            int timeZoneOffset,
            string expertName,
            string beverageName,
            string roadName,
            string dealerName,
            string dealerPhone)
        {
            VehicleTitle = vehicleTitle;
            BookingDateTimeUtc = bookingDateTimeUtc;
            TimeZoneOffset = timeZoneOffset;
            ExpertName = expertName;
            BeverageName = beverageName;
            RoadName = roadName;
            DealerName = dealerName;
            DealerPhone = dealerPhone;
        }

        public CompleteBookingSmsTemplate(Lead lead)
        {
            VehicleTitle = lead.CarTitle;
            BookingDateTimeUtc = lead.BookingDateTimeUtc;
            ExpertName = lead.Expert.Name;
            BeverageName = lead.Beverage.Name;
            RoadName = lead.Route.Name;
        }

        public string AsPlainText()
        {
            string bookingDateTime = "Skipped";
            if (BookingDateTimeUtc != null)
            {
                bookingDateTime = BookingDateTimeUtc.GetValueOrDefault()
                    .Add(new TimeSpan(0, -TimeZoneOffset, 0))
                    .ToString(QuantumLogicConstants.UsaTimeFormat, CultureInfo.InvariantCulture);
            }

            return $"Thank you! \n" +
                   $"Your Upcoming VIP Test Drive is Scheduled \n \n" +
                   $"Vehicle: {VehicleTitle} \n" +
                   $"Date & Time: {bookingDateTime} \n" +
                   $"Expert: {ExpertName} \n" +
                   $"Beverage: {BeverageName} \n" +
                   $"Route: {RoadName} \n\n" +
                   // TODO: customer comment
                   $"{DealerName} \n" +
                   $"{DealerPhone} \n";
        }
    }
}
