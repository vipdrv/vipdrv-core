using NUnit.Framework;
using QuantumLogic.Core.Utils.Email;
using QuantumLogic.Core.Utils.Email.Data;
using QuantumLogic.Core.Utils.Email.Data.Templates;
using SendGrid.Helpers.Mail;
using System;
using System.Collections.Generic;
using System.Threading.Tasks;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Email.Data.Templates.Arguments;
using QuantumLogic.WebApi.DataModels.Requests.Widget.Booking;

namespace QuantumLogic.Tests.Core.Utils.Email
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
            IEmailTemplate AdfEmailTemplate = new EleadAdfTemplate(
                DateTime.Now, 
                0, 
                firstName: "CAR TITLE", 
                secondName: "Evgeny",
                userPhone: "Platonov",
                userEmail: "+380666159567",
                siteName: "ultramarine256@gmail.com",
                dealerName: "Truck World",
                expertName: "VIN-111",
                beverageName: "DealerEmpire", 
                routeTitle: "ROUTE",
                dealerPeakSalesId: "000", 
                vehicle: new BookingVehicle());
            return testDriveEmailService.SendAdfEmail(recipientsList, AdfEmailTemplate);
        }

        [Test]
        public void EmailWithADFContent__ShouldGenerateValidXML()
        {
            IVehicle vehicle = new BookingVehicle();
            vehicle.Title = "BMW X5";
            vehicle.Make = "BMW";
            vehicle.Model = "X5";
            vehicle.Year = "2016";

            Lead lead = new Lead();

            IEmailTemplate eleadAdfTemplate = new EleadAdfTemplate(lead, vehicle, -120);
            string html = eleadAdfTemplate.AsHtml();
            Console.WriteLine(html);
        }
    }
}
