using System;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Booking;
using QuantumLogic.Data.EFContext;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Booking;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/booking")]
    public class BookingController : Controller
    {
        private readonly IBookingDomainService _bookingDomainService;

        public BookingController()
        {
            _bookingDomainService = new BookingDomainService();
        }

        [HttpPost("complete-booking")]
        public Task<string> CompleteBooking(long siteId, [FromBody]CompleteBookingRequest request)
        {
            
            //var firstName = request.BookingUser.FirstName;
            //var lastName = request.BookingUser.LastName;
            //var email = request.BookingUser.Email;
            //var phone = request.BookingUser.Phone;

            var carImgUrl = "https://generalstandart256.blob.core.windows.net/image-container/dummy-car-detail.jpg";
            var carTitle = "Nissan GT-R 2016 3.8 litre twin-turbo V6";

            //var expertName = request.BookingExpert.Name;
            //var beverageName = request.BookingBeverage.Name;
            //var roadName = request.BookingRoad.Name;

            //var sendGridProvider = new SendGridProvider();
            //var emailTemplate = sendGridProvider.EmailTemplate("Evgeny", "Platonov", DateTime.Now.ToString(), carTitle, "Fernando Alonso Díaz", "Tea", "Sea-Road");

            //sendGridProvider.SendEmail("ultramarine256@gmail.com", "Please confirm your Booking", emailTemplate);

            // TODO: send email
            // TODO: update leads table


            var db = new QuantumLogicDbContext();

            var lead2 = new Lead
            {
                FirstName = "Evgeny",
                SecondName = "Platonov",
                UserEmail = "ultramarine256@gmail.com",
                UserPhone = "+380953007952",
                SiteId = 13,
                RouteId = 4,
                ExpertId = 4,
                BeverageId = 6,

                RecievedUtc = DateTime.Now
            };

            db.Leads.Add(lead2);

            db.SaveChanges();


            return Task.FromResult("asd");
        }
    }
}
