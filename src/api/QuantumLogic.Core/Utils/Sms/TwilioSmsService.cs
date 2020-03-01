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
            _httpClient.DefaultRequestHeaders.Add("Authorization", "doodle-basic-auth");
            _apiUrl = "https://api.twilio.com/doodle-account";
        }

        public Task SendSms(IList<string> phoneNumbers, ISmsTemplate smsTemplate)
        {
            if (phoneNumbers.Count > 0)
            {
                List<Task> sendSMSTasks = new List<Task>();
                Dictionary<string, string> valueCollection = new Dictionary<string, string>();
                valueCollection.Add("From", "+12244123577");
                valueCollection.Add("Body", smsTemplate.AsPlainText());
                foreach (var phone in phoneNumbers)
                {
                    valueCollection["To"] = $"+{new String(phone.Where(Char.IsDigit).ToArray())}";
                    HttpRequestMessage message = new HttpRequestMessage(HttpMethod.Post, _apiUrl)
                    {
                        Content = new FormUrlEncodedContent(valueCollection)
                    };
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
