using System;
using System.Net;
using QuantumLogic.Core.Utils.Email.Templates;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public class TestDriveEmailService : ITestDriveEmailService // ILeadEmailService, IBookingEmailService
    {
        protected const string CompleteBookingSubject = "Thak you for complete booking";
        protected const string NewLeadNotificationSubject = "New Lead!";
        protected static EmailAddress EmailFrom = new EmailAddress("test@example.com", "Example User");
        protected IEmailProvider EmailProvider { get; private set; }

        protected const string InvitationEmailTemplate = "https://generalstandart256.blob.core.windows.net/testdrive-email-templates/dealer-invitation__email-template.html";

        public TestDriveEmailService(IEmailProvider emailProvider)
        {
            EmailProvider = emailProvider;
        }

        public HttpStatusCode SendTestDriveEmail(EmailAddress emailTo, string subject, IEmailTemplate emailTemplate)
        {
            if (emailTo == null)
            {
                throw new ArgumentException(nameof(emailTo));
            }
            if (emailTemplate == null)
            {
                throw new ArgumentException(nameof(emailTemplate));
            }

            return EmailProvider.SendEmail(emailTo, EmailFrom, subject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
        }

        //public HttpStatusCode SendTestDriveEmail(EmailAddress emailTo, string subject, string htmlContent, string plainTextContent = null)
        //{
        //    return EmailProvider.SendEmail(emailTo, EmailFrom, subject, plainTextContent, htmlContent);
        //}

        //public HttpStatusCode SendCompleteBookingEmail(EmailAddress emailTo, I obj, string plainTextContent = null)
        //{
        //    return SendTestDriveEmail(emailTo, CompleteBookingSubject, obj.GetBookingEmailTemplate(asd as ), plainTextContent);
        //}

        //public HttpStatusCode SendNewLeadNotificationEmail(EmailAddress emailTo, IEmailTemplate htmlContent, string plainTextContent = null)
        //{
        //    return SendTestDriveEmail(emailTo, NewLeadNotificationSubject, htmlContent, plainTextContent);
        //}

        public HttpStatusCode SendDealerInvitationEmail(EmailAddress emailTo, string invitationLink)
        {
            

            IEmailTemplate emailTemplate = new DealerInvitationEmailTemplate(InvitationEmailTemplate, invitationLink);

            return SendTestDriveEmail(emailTo, "INVITATION SUBJECT", emailTemplate);
        }

    }
}
