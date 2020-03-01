using QuantumLogic.Core.Utils.Sms.Templates;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.Sms
{
    public interface ISmsService
    {
        /// <summary>
        /// Is used to Send Sms
        /// </summary>
        /// <param name="recipientPhone">Recipients phone numbers with counry codes like 381112222222, +381112222222, +38 (111) 222-22-22</param>
        /// <param name="smsTemplate">SMS message template</param>
        Task SendSms(IList<string> recipientPhone, ISmsTemplate smsTemplate);
    }
}
