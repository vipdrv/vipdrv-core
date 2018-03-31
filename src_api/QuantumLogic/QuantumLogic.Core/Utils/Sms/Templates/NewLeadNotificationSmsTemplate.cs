using System;
using System.Globalization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Utils.Sms.Templates
{
    public class NewLeadNotificationSmsTemplate : ISmsTemplate
    {
        public string DealerName { get; }
        public string CustomerFirstName { get; }
        public string CustomerLastName { get; }
        public string CustomerPhone { get; }
        public string CustomerEmail { get; }
        public string VehicleTitle { get; }
        public string VehicleVin { get; }
        public DateTime? BookingDateTimeUtc { get; }
        public int TimeZoneOffset { get; }
        public string ExpertTitle { get; }
        public string BeverageTitle { get; }
        public string RoadTitle { get; }

        public NewLeadNotificationSmsTemplate(
            string dealerName,
            string customerFirstName,
            string customerLastName,
            string customerPhone,
            string customerEmail,
            string vehicleTitle,
            string vehicleVin,
            DateTime bookingDateTimeUtc,
            string expertTitle,
            string beverageTitle,
            string roadTitle)
        {
            CustomerFirstName = customerFirstName;
            DealerName = dealerName;
            CustomerLastName = customerLastName;
            CustomerPhone = customerPhone;
            CustomerEmail = customerEmail;
            VehicleTitle = vehicleTitle;
            VehicleVin = vehicleVin;
            BookingDateTimeUtc = bookingDateTimeUtc;
            ExpertTitle = expertTitle;
            BeverageTitle = beverageTitle;
            RoadTitle = roadTitle;
        }

        public NewLeadNotificationSmsTemplate(Lead lead, int timeZoneOffset = 0)
        {
            DealerName = lead.Site.DealerName;
            CustomerFirstName = lead.FirstName;
            CustomerLastName = lead.SecondName;
            CustomerPhone = lead.UserPhone;
            CustomerEmail = lead.UserEmail;
            VehicleTitle = lead.CarTitle;
            VehicleVin = lead.CarVin;
            BookingDateTimeUtc = lead.BookingDateTimeUtc;
            TimeZoneOffset = timeZoneOffset;
            ExpertTitle = (lead.Expert != null) ? lead.Expert.Name : "Skipped by customer";
            BeverageTitle = (lead.Beverage != null) ? lead.Beverage.Name : "Skipped by customer";
            RoadTitle = (lead.Route != null) ? lead.Route.Name : "Skipped by customer";
        }

        public string AsPlainText()
        {
            string bookingDateTime = "Skipped by customer";
            if (BookingDateTimeUtc != null)
            {
                bookingDateTime = BookingDateTimeUtc.GetValueOrDefault()
                    .Add(new TimeSpan(0, -TimeZoneOffset, 0))
                    .ToString(QuantumLogicConstants.UsaTimeFormat, CultureInfo.InvariantCulture);
            }

            return $"New Lead for {DealerName}! \n\n" +
                   
                   $"Vehicle: {VehicleTitle} \n" +
                   $"Vin: {VehicleVin} \n\n" +

                   "Customer \n" +
                   $"Name: {CustomerFirstName} {CustomerLastName} \n" +
                   $"Phone: {CustomerPhone} \n" +
                   $"Email: {CustomerEmail} \n\n" +
                   
                   "Booking details \n" +
                   $"Date & Time: {bookingDateTime} \n" +
                   $"Expert: {ExpertTitle} \n" +
                   $"Beverage: {BeverageTitle} \n" +
                   $"Route: {RoadTitle} \n";
        }
    }
}
