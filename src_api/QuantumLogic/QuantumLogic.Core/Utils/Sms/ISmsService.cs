using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.Sms
{
    public interface ISmsService
    {
        /// <summary>
        /// Is used to Send Sms
        /// </summary>
        /// <param name="to">Recipient phone number</param>
        /// <param name="content">SMS message content</param>
        Task<HttpResponseMessage> SendSms(string to, string content);
    }
}
