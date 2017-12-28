using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;
using QuantumLogic.Core.Utils.Sms.Templates;

namespace QuantumLogic.Core.Utils.Sms
{
    public interface ISmsService
    {
        /// <summary>
        /// Is used to Send Sms
        /// </summary>
        /// <param name="recipientPhone">Recipients phone numbers with counry codes like 381112222222, +381112222222, +38 (111) 222-22-22</param>
        /// <param name="smsTemplate">SMS message template</param>
        void SendSms(IList<string> recipientPhone, ISmsTemplate smsTemplate);
    }
}
