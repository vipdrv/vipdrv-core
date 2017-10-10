using System;
using System.Collections.Generic;
using System.Net;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using SendGrid;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Providers
{
    public class SendGridProvider : IEmailManager
    {
        private readonly SendGridClient _client;

        public SendGridProvider()
        {
            // TODO: move API Key to config file
            _client = new SendGridClient("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");
        }

        public string CompleteBookingEmailTemplate(string firstName, string lastName, string date, string vehicleTitle, string expert, string beverage, string road)
        {
            var templateUrl = "https://generalstandart256.blob.core.windows.net/image-container/email_template.html";

            var client = new HttpClient();
            
            // TODO: make async
            var html = client.GetStringAsync(templateUrl).Result;

            html = html.Replace("{{first_name}}", firstName);
            html = html.Replace("{{last_name}}", lastName);
            html = html.Replace("{{date}}", date);
            html = html.Replace("{{vehicle_title}}", vehicleTitle);
            html = html.Replace("{{expert}}", expert);
            html = html.Replace("{{beverage}}", beverage);
            html = html.Replace("{{road}}", road);

            return html;
        }

        public string SendEmail(string emailTo, string subject, string emailTemplate)
        {
            var from = new EmailAddress("support@vipdrv.com", "Example User");
            var to = new EmailAddress(emailTo, "Example User");
            
            var msg = MailHelper.CreateSingleEmail(from, to, subject, "", emailTemplate);

            // TODO: make async
            return _client.SendEmailAsync(msg).Result.ToString();
        }

    }
}
