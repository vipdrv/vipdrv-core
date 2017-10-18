using System.Net;
using QuantumLogic.Core.Utils.Email.Templates;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public interface ITestDriveEmailService
    {
        /// <summary>
        /// Is used to send Email wrapped by TestDrive
        /// </summary>
        /// <param name="emailTo">Recipient email</param>
        /// <param name="emailTemplate">Email Template</param>
        /// <returns>
        /// Returns <see cref="HttpStatusCode"/> of added Email to queue to Send
        /// </returns>
        HttpStatusCode SendTestDriveEmail(EmailAddress emailTo, string subject, IEmailTemplate emailTemplate);

HttpStatusCode SendDealerInvitationEmail(EmailAddress emailTo, string invitationLink);
    }
}
