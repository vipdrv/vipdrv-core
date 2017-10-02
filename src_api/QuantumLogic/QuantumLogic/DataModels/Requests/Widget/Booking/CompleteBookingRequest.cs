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
        public BookingExpert BookingExpert { get; set;}
        public BookingBeverage BookingBeverage { get; set; }
        public BookingRoad BookingRoad { get; set; }
        public BookingCar BookingCar { get; set; }

    }

    public class BookingUser
    {
        public string FirstName { get; set; }
        public string SecondName { get; set; }
        public string Phone { get; set; }
        public string Email { get; set; }
        public bool AllowToUsePhone { get; set; }
        public bool AllowToUseEmail { get; set; }
    }

    public class BookingDateTime
    {
        public DateTime DateTime { get; set; }
    }

    public class BookingExpert
    {
        public int Id { get; set; }
        public string Name { get; set; }
    }

    public class BookingBeverage
    {
        public int Id { get; set; }
        public string Name { get; set; }
    }

    public class BookingRoad
    {
        public int Id { get; set; }
        public string Name { get; set; }
    }

    public class BookingCar
    {
        public string CarImageUrl { get; set; }
        public string Title { get; set; }
    }
}
