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
        public DateTime? BookingDateTime { get; }
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
            DateTime bookingDateTime,
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
            BookingDateTime = bookingDateTime;
            ExpertTitle = expertTitle;
            BeverageTitle = beverageTitle;
            RoadTitle = roadTitle;
        }

        public NewLeadNotificationSmsTemplate(Lead lead)
        {
            DealerName = lead.Site.DealerName;
            CustomerFirstName = lead.FirstName;
            CustomerLastName = lead.SecondName;
            CustomerPhone = lead.UserPhone;
            CustomerEmail = lead.UserEmail;
            VehicleTitle = lead.CarTitle;
            BookingDateTime = lead.BookingDateTimeUtc;
            ExpertTitle = (lead.Expert != null) ? lead.Expert.Name : "Skipped by customer";
            BeverageTitle = (lead.Beverage != null) ? lead.Beverage.Name : "Skipped by customer";
            RoadTitle = (lead.Route != null) ? lead.Route.Name : "Skipped by customer";
        }

        public string AsPlainText()
        {
            return $"New Lead for {DealerName}! \n\n" +
                   
                   "Vehicle \n" +
                   $"Vehicle: {VehicleTitle} \n\n" +

                   "Customer \n" +
                   $"Name: {CustomerFirstName} {CustomerLastName} \n" +
                   $"Phone: {CustomerPhone} \n" +
                   $"Email: {CustomerEmail} \n\n" +
                   
                   "Booking details \n" +
                   $"Date & Time: {BookingDateTime.GetValueOrDefault().ToString(QuantumLogicConstants.UsaTimeFormat, CultureInfo.InvariantCulture)} \n" +
                   $"Expert: {ExpertTitle} \n" +
                   $"Beverage: {BeverageTitle} \n\n" +
                   $"Route: {RoadTitle} \n";
        }
    }
}
