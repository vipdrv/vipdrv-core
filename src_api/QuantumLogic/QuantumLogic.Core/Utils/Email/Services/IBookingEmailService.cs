using System.Net;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public interface IBookingEmailService : ITestDriveEmailService
    {
        HttpStatusCode SendCompleteBookingEmail(EmailAddress emailTo, IEmailTemplate htmlContent, string plainTextContent = null);
    }
}