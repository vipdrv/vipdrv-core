using System.Net.Http;

namespace QuantumLogic.Core.Utils.Email.Data.Templates
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
            var html = new HttpClient().GetStringAsync((string) TemplateUrl).Result;

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
