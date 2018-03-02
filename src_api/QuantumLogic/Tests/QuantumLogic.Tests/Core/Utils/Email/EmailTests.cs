using NUnit.Framework;
using QuantumLogic.Core.Utils.Email;
using QuantumLogic.Core.Utils.Email.Data;
using QuantumLogic.Core.Utils.Email.Data.Templates;
using SendGrid.Helpers.Mail;
using System;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace QuantumLogic.Tests.Core.Utils.Email
{
    [TestFixture]
    public sealed class EmailManagerTests
    {
        [Test]
        public Task EmailWithADFContent__ShouldBeValid()
        {
            ITestDriveEmailService testDriveEmailService = new TestDriveEmailService();
            List<EmailAddress> recipientsList = new List<EmailAddress>()
            {
                new EmailAddress("ultramarine256@gmail.com")
            };
            IEmailTemplate AdfEmailTemplate = new EleadAdfTemplate(DateTime.Now, 0, "CAR TITLE", "Evgeny", "Platonov", "+380666159567", "ultramarine256@gmail.com", "Truck World", "VIN-111", "DealerEmpire", "EXPERT - NAME", "BEVERAGE - NAME", "ROUTE - NAME");
            return testDriveEmailService.SendAdfEmail(recipientsList, AdfEmailTemplate);
        }
    }
}
