using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;

namespace QuantumLogic.Core.Utils.Email.Templates
{
    public class DealerInvitationEmailTemplate : TestDriveEmailTemplate
    {
        private readonly string _invitationLink;

        public DealerInvitationEmailTemplate(string templateUrl, string invitationLink) : base(templateUrl)
        {
            _invitationLink = invitationLink;
        }

        public override string AsHtml()
        {
            var html = GetTemplate().Result;

            html = html.Replace("{{invitationLink}}", _invitationLink);

            return html;
        }

        public override string AsPlainText()
        {
            return null;
        }
    }
}
