using System.Net;
using QuantumLogic.Core.Utils.Email.Templates;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public interface IBookingEmailService : ITestDriveEmailService
    {
        /// <summary>
        /// Is used to send confiramation Email to user after completing TestDrive booking
        /// </summary>
        /// <param name="emailTo"></param>
        /// <param name="htmlContent"></param>
        /// <param name="plainTextContent"></param>
        /// <returns>
        /// Returns <see cref="HttpStatusCode"/> of added Email to queue to Send
        /// </returns>
        HttpStatusCode SendCompleteBookingEmail(EmailAddress emailTo, ICompleteBookingEmailTemplate htmlContent, string plainTextContent = null);
    }
}