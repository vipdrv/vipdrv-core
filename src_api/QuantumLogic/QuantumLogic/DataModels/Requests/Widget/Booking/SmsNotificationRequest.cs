using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.DataModels.Requests.Widget.Booking
{
    public class SmsNotificationRequest
    {
        public string Phone { get; set; }
        public string DateTime { get; set; }
        public string VehicleTitle { get; set; }
        public string ExpertTitle { get; set; }
        public string BeverageTitle { get; set; }
        public string RoadTitle { get; set; }
    }
}
