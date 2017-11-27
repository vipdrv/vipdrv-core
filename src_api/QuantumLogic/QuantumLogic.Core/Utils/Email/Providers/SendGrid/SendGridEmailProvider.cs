using System.Collections.Generic;
using System.Net;
using SendGrid;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Providers.SendGrid
{
    public class SendGridEmailProvider : IEmailProvider
    {
        private readonly SendGridClient _client;
        
        public SendGridEmailProvider()
        {
            _client = new SendGridClient("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");
        }
        
        public HttpStatusCode SendEmail(EmailAddress emailTo, EmailAddress emailFrom, string subject, string plainTextContent, string htmlContent)
        {
            var msg = MailHelper.CreateSingleEmail(emailFrom, emailTo, subject, plainTextContent, htmlContent);

            return _client.SendEmailAsync(msg).Result.StatusCode;
        }

        public HttpStatusCode SendEmail(IList<EmailAddress> emailTo, EmailAddress emailFrom, string subject, string plainTextContent,
            string htmlContent)
        {
            throw new System.NotImplementedException();
        }
    }
}
