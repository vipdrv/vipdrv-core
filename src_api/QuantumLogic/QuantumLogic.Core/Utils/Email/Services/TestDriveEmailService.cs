using System;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public class TestDriveEmailService : ITestDriveEmailService
    {
        private readonly IEmailProvider _emailProvider;
        private readonly EmailAddress _emailFrom;

        public TestDriveEmailService(IEmailProvider emailProvider)
        {
            _emailProvider = emailProvider;
            _emailFrom = new EmailAddress("test@example.com", "Example User");
        }

        public string SendCompleteBookingEmail(EmailAddress emailTo, IEmailTemplate emailTemplate)
        {
            var subject = "Thak you for complete booking";

            return _emailProvider.SendEmail(emailTo, _emailFrom, subject, "", emailTemplate.AsHtml());
        }

        public string SendNewLeadNotificationEmail(EmailAddress emailTo, IEmailTemplate emailTemplate)
        {
            throw new NotImplementedException();
        }
    }
}
