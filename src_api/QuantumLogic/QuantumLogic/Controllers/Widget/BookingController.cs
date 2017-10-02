using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Booking;

namespace QuantumLogic.WebApi.Controllers.Widget
{
    [Route("api/booking")]
    public class BookingController
    {
        [HttpPost("complete-booking")]
        public Task<string> CompleteBooking(long siteId, [FromBody]CompleteBookingRequest request)
        {
            // TODO: send email
            // TODO: update leads table
            
            return Task.FromResult("asd");
        }
    }
}
