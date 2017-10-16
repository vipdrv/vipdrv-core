using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.Email.Templates
{
    class NewLeadNotificationEmailTemplate : IEmailTemplate
    {
        private readonly string _newLeadNotificationEmailTemplate;

        public NewLeadNotificationEmailTemplate()
        {
            _newLeadNotificationEmailTemplate = "https://generalstandart256.blob.core.windows.net/testdrive-email-templates/new-lead__email-template.html";
        }

        public string AsHtml()
        {
            throw new NotImplementedException();
        }

    }
}
