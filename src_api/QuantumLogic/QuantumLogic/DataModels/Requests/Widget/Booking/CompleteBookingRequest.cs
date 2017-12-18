using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Newtonsoft.Json;

namespace QuantumLogic.WebApi.DataModels.Requests.Widget.Booking
{
    public class CompleteBookingRequest
    {
        public BookingUser BookingUser { get; set; }
        public BookingDateTime Calendar { get; set; }
        public BookingCar BookingCar { get; set; }
        public int? ExpertId { get; set;}
        public int? BeverageId { get; set; }
        public int? RoadId { get; set; }

        public CompleteBookingRequest()
        {
            BookingUser = new BookingUser();
            Calendar = new BookingDateTime();
            BookingCar = new BookingCar();
        }
    }

    public class BookingUser
    {
        public string FirstName { get; set; }
        public string LastName { get; set; }
        public string Phone { get; set; }
        public string Email { get; set; }
    }

    public class BookingDateTime
    {
        public string Date { get; set; }
        public string Time { get; set; }
    }
    
    public class BookingCar
    {
        public string Vin { get; set; }
        public string ImageUrl { get; set; }
        public string Title { get; set; }
        public string Engine { get; set; }
        public string Year { get; set; }
        public string Color { get; set; }
        public string Transmission { get; set; }
        public string Fuel { get; set; }
    }
}
