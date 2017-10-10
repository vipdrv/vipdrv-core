using System;
using System.Collections.Generic;
using System.Text;
using System.Threading.Tasks;
using NUnit.Framework;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Email.Providers;
using QuantumLogic.Data.EFContext;
using SendGrid;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Tests.Core.Utils.Email
{
    [TestFixture]
    public sealed class EmailManagerTests
    {
        [Test]
        public void Test()
        {
            var sendGridProvider = new SendGridProvider();
            var emailTemplate = sendGridProvider.CompleteBookingEmailTemplate("A", "B");

            sendGridProvider.SendEmail("ultramarine256@gmail.com", "Please confirm your Booking", emailTemplate);


        }

        
    }
    
}
