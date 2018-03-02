using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.DataModels.Requests.Widget.Booking
{
    public class SmsNotificationRequest
    {
        public string Phone { get; set; }
        public DateTime? BookingDateTimeUtc { get; set; }
        public int TimeZoneOffset { get; set; }
        public string VehicleTitle { get; set; }
        public string ExpertName { get; set; }
        public string BeverageName { get; set; }
        public string RoadName { get; set; }
        public string DealerName { get; set; }
        public string DealerPhone { get; set; }
    }
}
