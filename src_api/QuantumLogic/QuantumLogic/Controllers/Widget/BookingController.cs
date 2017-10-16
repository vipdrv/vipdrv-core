using System;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Services.Widget.Booking;
using QuantumLogic.Core.Utils.Email;
using QuantumLogic.Core.Utils.Email.Providers.SendGrid;
using QuantumLogic.Core.Utils.Email.Services;
using QuantumLogic.Core.Utils.Email.Templates;
using QuantumLogic.Data.EFContext;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Booking;
using SendGrid.Helpers.Mail;

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
        public Task<bool> CompleteBooking(int siteId, [FromBody]CompleteBookingRequest request)
        {
            ITestDriveEmailService driveEmailService = new TestDriveEmailService(new SendGridEmailProvider());
            var emailTo = new EmailAddress(request.BookingUser.Email, $"{request.BookingUser.FirstName} {request.BookingUser.LastName}");
            IEmailTemplate emailTemplate = new CompleteBookingEmailTemplate(request.BookingUser.FirstName, request.BookingUser.LastName, DateTime.Now.ToString(), "Ford Mustang", "Expert #1", "Tea", "City Roads");
            
            var db = new QuantumLogicDbContext();
            var lead = new Lead(0, siteId, request.BookingExpert.Id, request.BookingBeverage.Id, request.BookingRoad.Id, DateTime.Now, request.BookingUser.FirstName, request.BookingUser.LastName, request.BookingUser.Phone, request.BookingUser.Email);
            db.Leads.Add(lead);

            db.SaveChanges();
            
            return Task.FromResult(true);
        }
    }
}
