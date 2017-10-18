using System.Net;
using QuantumLogic.Core.Utils.Email.Templates;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public interface ILeadEmailService : ITestDriveEmailService
    {
        /// <summary>
        /// Is used to notify Dealer about new TestDrive booking
        /// </summary>
        /// <param name="emailTo"></param>
        /// <param name="htmlContent"></param>
        /// <param name="plainTextContent"></param>
        /// <returns>
        /// Returns <see cref="HttpStatusCode"/> of added Email to queue to Send
        /// </returns>
        HttpStatusCode SendNewLeadNotificationEmail(EmailAddress emailTo, INewLeadNotificationEmailTemplate htmlContent, string plainTextContent = null);
    }
}