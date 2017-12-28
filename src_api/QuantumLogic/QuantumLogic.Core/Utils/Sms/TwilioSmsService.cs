using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;
using QuantumLogic.Core.Utils.Sms.Templates;

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

        public void SendSms(IList<string> phoneNumbers, ISmsTemplate smsTemplate)
        {
            Dictionary<string, string> valueCollection = new Dictionary<string, string>();

            valueCollection.Add("From", "+12244123577");
            valueCollection.Add("Body", smsTemplate.AsPlainText());
            valueCollection.Add("To", "");

            foreach (var phone in phoneNumbers)
            {
                valueCollection["To"] = phone;
                _httpClient.SendAsync(new HttpRequestMessage(HttpMethod.Post, _apiUrl) { Content = new FormUrlEncodedContent(valueCollection) });
            }
        }
    }
}
