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
    }
}
