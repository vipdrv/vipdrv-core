using System;
using System.Collections.Generic;
using System.Text;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Utils.Sms.Templates
{
    public class CompleteBookingSmsTemplate : ISmsTemplate
    {
        public string VehicleTitle { get; }
        public DateTime? BookingDateTime { get; }
        public string ExpertName { get; }
        public string BeverageName { get; }
        public string RoadName { get; }
        public string DealerName { get; set; }
        public string DealerPhone { get; set; }

        public CompleteBookingSmsTemplate(
            string vehicleTitle,
            DateTime? bookingDateTime,
            string expertName,
            string beverageName,
            string roadName,
            string dealerName,
            string dealerPhone)
        {
            VehicleTitle = vehicleTitle;
            BookingDateTime = bookingDateTime;
            ExpertName = expertName;
            BeverageName = beverageName;
            RoadName = roadName;
            DealerName = dealerName;
            DealerPhone = dealerPhone;
        }

        public CompleteBookingSmsTemplate(Lead lead)
        {
            VehicleTitle = lead.CarTitle;
            BookingDateTime = lead.BookingDateTimeUtc;
            ExpertName = lead.Expert.Name;
            BeverageName = lead.Beverage.Name;
            RoadName = lead.Route.Name;
        }

        public string AsPlainText()
        {
            return $"Thank you! \n" +
                   $"Your Upcoming VIPdrv Test Drive is Scheduled \n \n" +
                   $"Vehicle: {VehicleTitle} \n" +
                   $"Date & Time: {BookingDateTime.GetValueOrDefault().ToString(QuantumLogicConstants.OutputDateTimeFormat)} \n" +
                   $"Expert: {ExpertName} \n" +
                   $"Beverage: {BeverageName} \n" +
                   $"Route: {RoadName} \n\n" +
                   $"{DealerName} \n" +
                   $"{DealerPhone} \n";
        }
    }
}
