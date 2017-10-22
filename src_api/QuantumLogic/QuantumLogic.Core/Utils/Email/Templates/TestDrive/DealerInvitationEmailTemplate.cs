using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Runtime.CompilerServices;
using System.Text;
using OfficeOpenXml;

namespace QuantumLogic.Core.Utils.Email.Templates.TestDrive
{
    public class DealerInvitationEmailTemplate : IEmailTemplate
    {
        private readonly string _invitationLink;
        protected const string TemplateUrl = "https://generalstandart256.blob.core.windows.net/testdrive-email-templates/dealer-invitation__email-template.html";

        public DealerInvitationEmailTemplate(string invitationLink)
        {
            _invitationLink = invitationLink;
        }

        public string AsHtml()
        {
            // TODO: use method as async
            var html = new HttpClient().GetStringAsync(TemplateUrl).Result;

            html = html.Replace("{{invitationLink}}", _invitationLink);

            return html;
        }

        public string AsPlainText()
        {
            // TODO: implement plain text email content
            return "";
        }
    }
}
