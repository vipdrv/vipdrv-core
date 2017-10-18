using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.Email.Templates
{
    public abstract class TestDriveEmailTemplate : IEmailTemplate
    {
        public string TemplateUrl { get; private set; }

        public TestDriveEmailTemplate(string templateUrl)
        {
            TemplateUrl = templateUrl;
        }

        protected Task<string> GetTemplate()
        {
            return new HttpClient().GetStringAsync(TemplateUrl);
        }

        public abstract string AsHtml();
        public abstract string AsPlainText();
    }
}
