using QuantumLogic.Core.Utils.Email.Data;
using SendGrid;
using SendGrid.Helpers.Mail;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.Email
{
    public interface ITestDriveEmailService
    {
        /// <summary>
        /// Is used to send invitation link for new Dealer
        /// </summary>
        /// <param name="emailTo">email address to send</param>
        /// <param name="emailTemplate">email template</param>
        /// <returns>Returns task with result of operation (add Email to queue to Send) like <see cref="SendGrid.Response"/></returns>
        Task<Response> SendDealerInvitationEmail(EmailAddress emailTo, IEmailTemplate emailTemplate);

        /// <summary>
        /// Is used to send confirmation Email to customer that completed TestDrive booking
        /// </summary>
        /// <param name="emailTo">email address to send</param>
        /// <param name="emailTemplate">email template</param>
        /// <returns>Returns task with result of operation (add Email to queue to Send) like <see cref="SendGrid.Response"/></returns>
        Task<Response> SendCompleteBookingEmail(EmailAddress emailTo, IEmailTemplate emailTemplate);

        /// <summary>
        /// Is used to notify Dealer about new customer that completed TestDrive booking
        /// </summary>
        /// <param name="emailTo">list of email addresses to send</param>
        /// <param name="emailTemplate">email template</param>
        /// <returns>Returns task with result of operation (add Email to queue to Send) like <see cref="SendGrid.Response"/></returns>
        Task<Response> SendNewLeadNotificationEmail(List<EmailAddress> emailTo, IEmailTemplate emailTemplate);

        /// <summary>
        /// Is used to notify Expert about Test Drive appointment with his involvement
        /// </summary>
        /// <param name="emailTo">list of email addresses to send</param>
        /// <param name="emailTemplate">email template</param>
        /// <returns>Returns task with result of operation (add Email to queue to Send) like <see cref="SendGrid.Response"/></returns>
        Task<Response> SendExpertNotificationEmail(List<EmailAddress> emailTo, IEmailTemplate emailTemplate);

        /// <summary>
        /// Is used to send Auto-lead Data Format to Dealer CRM in Xml format
        /// </summary>
        /// <param name="emailTo">list of email addresses to send</param>
        /// <param name="emailTemplate">email template</param>
        /// <returns>Returns task with result of operation (add Email to queue to Send) like <see cref="SendGrid.Response"/></returns>
        Task<Response> SendAdfEmail(List<EmailAddress> emailTo, IEmailTemplate emailTemplate);
    }
}
