using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.DataModels.Requests.Widget.Booking
{
    public class CompleteBookingRequest
    {
        public BookingUser BookingUser { get; set; }
        public BookingDateTime BookingDateTime { get; set; }
        public BookingCar BookingCar { get; set; }
        public int ExpertId { get; set;}
        public int BeverageId { get; set; }
        public int RoadId { get; set; }

        public CompleteBookingRequest()
        {
            BookingUser = new BookingUser();
            BookingDateTime = new BookingDateTime();
            BookingCar = new BookingCar();
        }
    }

    public class BookingUser
    {
        public string FirstName { get; set; }
        public string LastName { get; set; }
        public string Phone { get; set; }
        public string Email { get; set; }
        public bool AllowToUsePhone { get; set; }
        public bool AllowToUseEmail { get; set; }
    }

    public class BookingDateTime
    {
        public DateTime DateTime { get; set; }
    }
    
    public class BookingCar
    {
        public string CarVIN { get; set; }
        public string CarImageUrl { get; set; }
        public string Title { get; set; }
    }
}
