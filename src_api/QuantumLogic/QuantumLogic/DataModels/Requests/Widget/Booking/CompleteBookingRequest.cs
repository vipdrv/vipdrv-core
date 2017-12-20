using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Newtonsoft.Json;
using QuantumLogic.WebApi.DataModels.Dtos.Widget.Leads;

namespace QuantumLogic.WebApi.DataModels.Requests.Widget.Booking
{
    public class CompleteBookingRequest
    {
        public BookingUser BookingUser { get; set; }
        public DateTime BookingDateTime { get; set; }
        public BookingCar BookingCar { get; set; }
        public int? ExpertId { get; set; }
        public int? BeverageId { get; set; }
        public int? RoadId { get; set; }

        public CompleteBookingRequest()
        {
            BookingUser = new BookingUser();
            BookingCar = new BookingCar();
        }

        public virtual LeadFullDto MapToLeadFullDto(int siteId)
        {
            return new LeadFullDto(
                0,
                siteId,
                ExpertId,
                BeverageId,
                RoadId,
                BookingUser.FirstName,
                BookingUser.LastName,
                BookingUser.Phone,
                BookingUser.Email,
                BookingCar.ImageUrl,
                BookingCar.Title,
                BookingCar.Vin,
                BookingDateTime);
        }
    }

    public class BookingUser
    {
        public string FirstName { get; set; }
        public string LastName { get; set; }
        public string Phone { get; set; }
        public string Email { get; set; }
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
