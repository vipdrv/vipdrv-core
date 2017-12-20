using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.Sms.Templates
{
    public interface ISmsTemplate
    {
        /// <summary>
        /// Is used to get SMS template as plain text
        /// </summary>
        /// <returns>
        /// string with plain text
        /// </returns>
        string AsPlainText();
    }
}
