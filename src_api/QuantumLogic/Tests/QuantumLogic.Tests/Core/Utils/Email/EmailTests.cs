using System;
using System.Collections;
using System.Collections.Generic;
using System.Globalization;
using System.Linq;
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
        public void EmailWithADFContent__ShouldBeValid()
        {
            ITestDriveEmailService testDriveEmailService = new TestDriveEmailService();
            IList<EmailAddress> recipientsList =
                new List<EmailAddress>() {new EmailAddress("ultramarine256@gmail.com")};
            IEmailTemplate AdfEmailTemplate = new EleadAdfTemplate(DateTime.Now, "CAR TITLE", "Evgeny", "Platonov", "+380666159567", "ultramarine256@gmail.com", "Truck World", "VIN-111", "DealerEmpire", "EXPERT - NAME", "BEVERAGE - NAME", "ROUTE - NAME");


            testDriveEmailService.SendAdfEmail(recipientsList, AdfEmailTemplate);

        }
    }
}
