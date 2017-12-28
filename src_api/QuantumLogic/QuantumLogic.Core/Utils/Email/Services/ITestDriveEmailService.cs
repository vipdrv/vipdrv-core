using System.Collections.Generic;
using System.Net;
using QuantumLogic.Core.Utils.Email.Templates;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public interface ITestDriveEmailService
    {
        /// <summary>
        /// Is used to send empty TestDrive email
        /// </summary>
        /// <param name="emailTo"></param>
        /// <param name="subject"></param>
        /// <param name="emailTemplate"></param>
        /// <returns></returns>
        HttpStatusCode SendTestDriveEmail(EmailAddress emailTo, string subject, IEmailTemplate emailTemplate);

        /// <summary>
        /// Is used to send invitation link for new Dealer
        /// </summary>
        /// <param name="emailTo"></param>
        /// <param name="emailTemplate"></param>
        /// <returns>
        /// Returns <see cref="HttpStatusCode"/> of added Email to queue to Send
        /// </returns>
        HttpStatusCode SendDealerInvitationEmail(EmailAddress emailTo, IEmailTemplate emailTemplate);

        /// <summary>
        /// Is used to send confirmation Email to customer that completed TestDrive booking
        /// </summary>
        /// <param name="emailTo"></param>
        /// <param name="emailTemplate"></param>
        /// <returns>
        /// Returns <see cref="HttpStatusCode"/> of added Email to queue to Send
        /// </returns>
        HttpStatusCode SendCompleteBookingEmail(EmailAddress emailTo, IEmailTemplate emailTemplate);

        /// <summary>
        /// Is used to notify Dealer about new customer that completed TestDrive booking
        /// </summary>
        /// <param name="emailTo"></param>
        /// <param name="emailTemplate"></param>
        /// <returns>
        /// Returns <see cref="HttpStatusCode"/> of added Email to queue to Send
        /// </returns>
        HttpStatusCode SendNewLeadNotificationEmail(EmailAddress emailTo, IEmailTemplate emailTemplate);

        /// <summary>
        /// Is used to send Auto-lead Data Format to Dealer CRM in Xml format
        /// </summary>
        /// <param name="emailTo">email address of CRM</param>
        /// <param name="emailTemplate"></param>
        /// <returns></returns>
        HttpStatusCode SendAdfEmail(EmailAddress emailTo, IEmailTemplate emailTemplate);
    }
}
