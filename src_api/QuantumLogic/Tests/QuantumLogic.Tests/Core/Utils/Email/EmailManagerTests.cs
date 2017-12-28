using System;
using System.Collections.Generic;
using System.Globalization;
using System.Text;
using System.Threading.Tasks;
using NUnit.Framework;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Email;
using QuantumLogic.Core.Utils.Email.Data;
using QuantumLogic.Core.Utils.Email.Data.Templates;
using QuantumLogic.Data.EFContext;
using SendGrid;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Tests.Core.Utils.Email
{
    [TestFixture]
    public sealed class EmailManagerTests
    {
        [Test]
        // [Ignore("Real Email")]
        public void AdfFormat__ShouldComposeXmlMessage()
        {
            IEmailTemplate adfTemplate = new EleadAdfTemplate(DateTime.Now, "Tilte", "Vin", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE");
            SendGridClient _client = new SendGridClient("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");

            var msg = MailHelper.CreateSingleEmail(new EmailAddress("ultramarine256@gmail.com"), new EmailAddress("ultramarine256@gmail.com"), "Subject", "", "");
            msg.AddContent("text/plain", adfTemplate.AsHtml());

            Response result = _client.SendEmailAsync(msg).Result;
        }

        [Test]
        public void Test()
        {
            
        }

    }
}
