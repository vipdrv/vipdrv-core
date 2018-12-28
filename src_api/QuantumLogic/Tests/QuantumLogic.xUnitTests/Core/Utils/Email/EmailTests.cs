using System;
using System.Collections.Generic;
using System.Threading.Tasks;
using NUnit.Framework;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Email;
using QuantumLogic.Core.Utils.Email.Data;
using QuantumLogic.Core.Utils.Email.Data.Templates;
using QuantumLogic.Core.Utils.Email.Data.Templates.Arguments;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Booking;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.xUnitTests.Core.Utils.Email
{
    [TestFixture]
    public sealed class EmailManagerTests
    {
        [Test]
        public Task EmailWithADFContent__ShouldSendViaEmail()
        {
            ITestDriveEmailService testDriveEmailService = new TestDriveEmailService();
            List<EmailAddress> recipientsList = new List<EmailAddress>()
            {
                new EmailAddress("ultramarine256@gmail.com")
            };

            var vehicle = new BookingVehicle();
            vehicle.Title = "2018 New BMW X5";
            vehicle.Vin = "WAUJT68E23A269563";
            vehicle.Stock = "FC7310B";

            IEmailTemplate adfEmailTemplate = new EleadAdfTemplate(
                DateTime.Now, 
                0, 
                firstName: "Evgeny", 
                secondName: "Platonov",
                userPhone: "+380666159567",
                userEmail: "ultramarine256@gmail.com",
                userComments: "Test User comments",
                siteName: "Truck World",
                dealerName: "Truck World",
                expertName: "Expert Name",
                beverageName: "Beverage Name", 
                routeTitle: "Route Name",
                dealerPeakSalesId: "000", 
                vehicle: vehicle);
            return testDriveEmailService.SendAdfEmail(recipientsList, adfEmailTemplate);
        }

        [Test]
        // [Ignore("Is used to debug Email content")]
        public void EmailWithADFContent__ShouldGenerateValidXML()
        {
            ITestDriveEmailService testDriveEmailService = new TestDriveEmailService();
            List<EmailAddress> recipientsList = new List<EmailAddress>()
            {
                new EmailAddress("ultramarine256@gmail.com")
            };

            IVehicle vehicle = new BookingVehicle();
            vehicle.Title = "2018 New BMW X5";
            vehicle.Vin = "WAUJT68E23A269563";
            vehicle.Stock = "FC7310B";

            Lead lead = new Lead();

            IEmailTemplate eleadAdfTemplate = new EleadAdfTemplate(lead, vehicle, -120);
            string xml = eleadAdfTemplate.AsPlainText();

            testDriveEmailService.SendAdfEmail(recipientsList, eleadAdfTemplate);
            Console.WriteLine(xml);
        }
    }
}
