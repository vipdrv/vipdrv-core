using System.Net;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public interface ITestDriveEmailService
    {
        /// <summary>
        /// Is used to send Email wrapped by TestDrive
        /// </summary>
        /// <param name="emailTo">Recipient email</param>
        /// <param name="subject">Email subject</param>
        /// <param name="htmlContent">Email HTML content</param>
        /// <param name="plainTextContent">Email Text content</param>
        /// <returns>
        /// Returns <see cref="HttpStatusCode"/> of added Email to queue to Send
        /// </returns>
        HttpStatusCode SendTestDriveEmail(EmailAddress emailTo, string subject, IEmailTemplate htmlContent, string plainTextContent = null);
    }
}
