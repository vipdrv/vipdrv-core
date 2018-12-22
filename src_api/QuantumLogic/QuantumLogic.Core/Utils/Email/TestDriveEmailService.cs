using QuantumLogic.Core.Utils.Email.Data;
using SendGrid;
using SendGrid.Helpers.Mail;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.Email
{
    public class TestDriveEmailService : ITestDriveEmailService
    {
        protected const string CompleteBookingSubject = "Your Upcoming Test Drive";
        protected const string NewLeadNotificationSubject = "New Test Drive Appointment";
        protected const string ExpertNotificationSubject = "New Test Drive Appointment";
        protected const string DealerInvitationSubject = "Welcome to TetsDrive";
        protected const string AdfEmailSubject = "ADF XML";
        protected EmailAddress EmailFrom = new EmailAddress("testdrive@vipdrv.com", "VIPdrv - VIP Test Drive");
        protected readonly ISendGridClient SendGridClient;

        public TestDriveEmailService()
        {
            SendGridClient = new SendGridClient("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");
        }

        public Task<Response> SendDealerInvitationEmail(EmailAddress emailTo, IEmailTemplate emailTemplate)
        {
            SendGridMessage message = MailHelper.CreateSingleEmail(EmailFrom, emailTo, DealerInvitationSubject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
            return SendGridClient.SendEmailAsync(message);
        }

        public Task<Response> SendCompleteBookingEmail(EmailAddress emailTo, IEmailTemplate emailTemplate)
        {
            SendGridMessage message = MailHelper.CreateSingleEmail(EmailFrom, emailTo, CompleteBookingSubject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
            return SendGridClient.SendEmailAsync(message);
        }

        public Task<Response> SendNewLeadNotificationEmail(List<EmailAddress> emailTo, IEmailTemplate emailTemplate)
        {
            SendGridMessage message = MailHelper.CreateSingleEmailToMultipleRecipients(EmailFrom, emailTo, NewLeadNotificationSubject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
            return SendGridClient.SendEmailAsync(message);
        }

        public Task<Response> SendExpertNotificationEmail(List<EmailAddress> emailTo, IEmailTemplate emailTemplate)
        {
            SendGridMessage message = MailHelper.CreateSingleEmailToMultipleRecipients(EmailFrom, emailTo, NewLeadNotificationSubject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
            return SendGridClient.SendEmailAsync(message);
        }

        public Task<Response> SendAdfEmail(List<EmailAddress> emailTo, IEmailTemplate emailTemplate)
        {
            SendGridMessage message = MailHelper.CreateSingleEmailToMultipleRecipients(EmailFrom, emailTo, AdfEmailSubject, emailTemplate.AsPlainText(), null);

            SendGridMessage sendGridMessage = new SendGridMessage();
            sendGridMessage.SetFrom(EmailFrom);
            sendGridMessage.SetGlobalSubject(AdfEmailSubject);
            if (!string.IsNullOrEmpty(emailTemplate.AsPlainText()))
            {
                sendGridMessage.AddContent("text/xml", emailTemplate.AsPlainText());
            }
            
            //if (!string.IsNullOrEmpty(htmlContent))
            //    sendGridMessage.AddContent(MimeType.Html, htmlContent);
            for (int personalizationIndex = 0; personalizationIndex < emailTo.Count; ++personalizationIndex)
                sendGridMessage.AddTo(emailTo[personalizationIndex], personalizationIndex);
            // return sendGridMessage;


            return SendGridClient.SendEmailAsync(sendGridMessage);
        }
    }
}
