using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using QuantumLogic.Core.Utils.Email.Data;
using SendGrid;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email
{
    public class TestDriveEmailService : ITestDriveEmailService
    {
        protected const string CompleteBookingSubject = "You Upcoming Test Drive";
        protected const string NewLeadNotificationSubject = "New Lead!";
        protected const string DealerInvitationSubject = "Welcome to TetsDrive";
        protected const string AdfEmailSubject = "VIPdrv - ADF XML ";
        protected EmailAddress EmailFrom = new EmailAddress("no-reply@vipdrv.com", "VIPdrv - VIP Test Drive");
        protected readonly ISendGridClient SendGridClient;

        public TestDriveEmailService()
        {
            SendGridClient = new SendGridClient("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");
        }

        public HttpStatusCode SendDealerInvitationEmail(EmailAddress emailTo, IEmailTemplate emailTemplate)
        {
            SendGridMessage message = MailHelper.CreateSingleEmail(EmailFrom, emailTo, DealerInvitationSubject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
            return SendGridClient.SendEmailAsync(message).Result.StatusCode;
        }

        public HttpStatusCode SendCompleteBookingEmail(EmailAddress emailTo, IEmailTemplate emailTemplate)
        {
            SendGridMessage message = MailHelper.CreateSingleEmail(EmailFrom, emailTo, CompleteBookingSubject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
            return SendGridClient.SendEmailAsync(message).Result.StatusCode;
        }

        public HttpStatusCode SendNewLeadNotificationEmail(IList<EmailAddress> emailTo, IEmailTemplate emailTemplate)
        {
            SendGridMessage message = MailHelper.CreateSingleEmailToMultipleRecipients(EmailFrom, emailTo.ToList(), NewLeadNotificationSubject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
            return SendGridClient.SendEmailAsync(message).Result.StatusCode;
        }

        public HttpStatusCode SendAdfEmail(IList<EmailAddress> emailTo, IEmailTemplate emailTemplate)
        {
            SendGridMessage message = MailHelper.CreateSingleEmailToMultipleRecipients(EmailFrom, emailTo.ToList(), NewLeadNotificationSubject, null, null);
            message.AddContent("text/plain", emailTemplate.AsPlainText());
            return SendGridClient.SendEmailAsync(message).Result.StatusCode;
        }
    }
}
