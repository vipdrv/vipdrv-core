using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.Email.Templates
{
    public interface IEmailTemplate
    {
        string AsHtml();
        string AsPlainText();
    }
}
