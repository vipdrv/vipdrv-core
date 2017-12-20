using System;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Utils.Sms.Templates
{
    public class NewLeadNotificationSmsTemplate : ISmsTemplate
    {
        public string VehicleTitle { get; }
        public DateTime BookingDateTime { get; }
        public string ExpertTitle { get; }
        public string BeverageTitle { get; }
        public string RoadTitle { get; }

        public NewLeadNotificationSmsTemplate(
            string vehicleTitle,
            DateTime bookingDateTime,
            string expertTitle,
            string beverageTitle,
            string roadTitle)
        {
            VehicleTitle = vehicleTitle;
            BookingDateTime = bookingDateTime;
            ExpertTitle = expertTitle;
            BeverageTitle = beverageTitle;
            RoadTitle = roadTitle;
        }

        public NewLeadNotificationSmsTemplate(Lead lead)
        {
            VehicleTitle = lead.CarTitle;
            BookingDateTime = lead.BookingDateTimeUtc;
            ExpertTitle = lead.Expert.Name;
            BeverageTitle = lead.Beverage.Name;
            RoadTitle = lead.Route.Name;
        }

        public string AsPlainText()
        {
            return "Your Upcoming Test Drive \n \n" +
                   $"Vehicle: {VehicleTitle} \n" +
                   $"Date & Time: {BookingDateTime.ToString(QuantumLogicConstants.OutputDateTimeFormat)} \n" +
                   $"Expert: {ExpertTitle} \n" +
                   $"Beverage: {BeverageTitle} \n" +
                   $"Road: {RoadTitle} \n";
        }
    }
}
