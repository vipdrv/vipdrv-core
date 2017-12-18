using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.Sms
{
    public class TwilioSmsService : ISmsService
    {
        private HttpClient _httpClient;
        private readonly string _apiUrl;
        private readonly string _authHeader;

        public TwilioSmsService()
        {
            _httpClient = new HttpClient();
            _apiUrl = "https://api.twilio.com/2010-04-01/Accounts/AC954e436269f73c312f8ae0d87ec886be/Messages.json";
            _authHeader = "Basic QUM5NTRlNDM2MjY5ZjczYzMxMmY4YWUwZDg3ZWM4ODZiZTpiODIzNWM1ZGRjM2EwMWQwMmI2YzFmNjhlZGVjZWM4Ng==";
        }

        public Task<HttpResponseMessage> SendSms(string to, string content)
        {
            var valueCollection = new Dictionary<string, string>();
            valueCollection.Add("To", to);
            valueCollection.Add("From", "+12244123577");
            valueCollection.Add("Body", content);

            _httpClient.DefaultRequestHeaders.Add("Authorization", _authHeader);
            var req = new HttpRequestMessage(HttpMethod.Post, _apiUrl) { Content = new FormUrlEncodedContent(valueCollection) };

            return _httpClient.SendAsync(req);
        }
    }
}
