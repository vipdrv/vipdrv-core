using QuantumLogic.Core.Utils.Sms.Templates;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.Sms
{
    public class TwilioSmsService : ISmsService
    {
        private readonly HttpClient _httpClient;
        private readonly string _apiUrl;

        public TwilioSmsService()
        {
            _httpClient = new HttpClient();
            _httpClient.DefaultRequestHeaders.Add("Authorization", "Basic QUM5NTRlNDM2MjY5ZjczYzMxMmY4YWUwZDg3ZWM4ODZiZTpiODIzNWM1ZGRjM2EwMWQwMmI2YzFmNjhlZGVjZWM4Ng==");
            _apiUrl = "https://api.twilio.com/2010-04-01/Accounts/AC954e436269f73c312f8ae0d87ec886be/Messages.json";
        }

        public Task SendSms(IList<string> phoneNumbers, ISmsTemplate smsTemplate)
        {
            if (phoneNumbers.Count > 0)
            {
                List<Task> sendSMSTasks = new List<Task>();
                Dictionary<string, string> valueCollection = new Dictionary<string, string>();
                valueCollection.Add("From", "+12244123577");
                valueCollection.Add("Body", smsTemplate.AsPlainText());
                valueCollection.Add("To", "");
                HttpRequestMessage message = new HttpRequestMessage(HttpMethod.Post, _apiUrl)
                {
                    Content = new FormUrlEncodedContent(valueCollection)
                };
                foreach (var phone in phoneNumbers)
                {
                    valueCollection["To"] = $"+{new String(phone.Where(Char.IsDigit).ToArray())}";
                    sendSMSTasks.Add(_httpClient.SendAsync(message));
                }
                return Task.WhenAll(sendSMSTasks);
            }
            else
            {
                return Task.CompletedTask;
            }
        }
    }
}
