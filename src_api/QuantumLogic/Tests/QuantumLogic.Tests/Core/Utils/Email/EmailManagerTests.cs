using System;
using System.Collections.Generic;
using System.Globalization;
using System.Text;
using System.Threading.Tasks;
using NUnit.Framework;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Email;
using QuantumLogic.Core.Utils.Email.Providers.SendGrid;
using QuantumLogic.Core.Utils.Email.Services;
using QuantumLogic.Core.Utils.Email.Templates;
using QuantumLogic.Data.EFContext;
using SendGrid;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Tests.Core.Utils.Email
{
    [TestFixture]
    public sealed class EmailManagerTests
    {
        [Test]
        [Ignore("Real Email")]
        public void AdfFormat__ShouldComposeXmlMessage()
        {
            IEmailTemplate adfTemplate = new EleadAdfTemplate(DateTime.Now, "Tilte", "Vin", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE", "TEST-VALUE");

            string html = adfTemplate.AsHtml();
            
            new TestDriveEmailService(new SendGridEmailProvider()).SendAdfEmail(new EmailAddress("ultramarine256@gmail.com"), adfTemplate);
        }
    }
}
