using System;
using System.Collections.Generic;
using System.Text;
using System.Threading.Tasks;
using NUnit.Framework;
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
        // [Ignore("Real Email sending")]
        public void CompleteBooking__ShouldSendEmail()
        {
            ITestDriveEmailService driveEmailService = new TestDriveEmailService(new SendGridEmailProvider());

            var emailTo = new EmailAddress("ultramarine256@gmail.com", "Evgeny Platonov");
            driveEmailService.SendDealerInvitationEmail(emailTo, "http://dev.admin.testdrive.pw/");
        }
    }
}
