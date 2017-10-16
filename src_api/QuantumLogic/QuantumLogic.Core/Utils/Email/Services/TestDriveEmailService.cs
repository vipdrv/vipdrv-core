using System;
using System.Net;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public class TestDriveEmailService : ITestDriveEmailService, ILeadEmailService, IBookingEmailService
    {
        protected const string CompleteBookingSubject = "Thak you for complete booking";
        protected const string NewLeadNotificationSubject = "New Lead!";
        protected static EmailAddress EmailFrom = new EmailAddress("test@example.com", "Example User");
        protected IEmailProvider EmailProvider { get; private set; }

        public TestDriveEmailService(IEmailProvider emailProvider)
        {
            EmailProvider = emailProvider;
        }

        public HttpStatusCode SendTestDriveEmail(EmailAddress emailTo, string subject, IEmailTemplate htmlContent, string plainTextContent = null)
        {
            return EmailProvider.SendEmail(emailTo, EmailFrom, subject, plainTextContent, htmlContent.AsHtml());
        }

        public HttpStatusCode SendCompleteBookingEmail(EmailAddress emailTo, IEmailTemplate htmlContent, string plainTextContent = null)
        {
            return SendTestDriveEmail(emailTo, CompleteBookingSubject, htmlContent, plainTextContent);
        }

        public HttpStatusCode SendNewLeadNotificationEmail(EmailAddress emailTo, IEmailTemplate htmlContent, string plainTextContent = null)
        {
            return SendTestDriveEmail(emailTo, NewLeadNotificationSubject, htmlContent, plainTextContent);
        }
    }
}
