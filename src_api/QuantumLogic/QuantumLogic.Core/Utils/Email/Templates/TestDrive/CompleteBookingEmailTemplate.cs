using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;

namespace QuantumLogic.Core.Utils.Email.Templates.TestDrive
{
    public class CompleteBookingEmailTemplate : IEmailTemplate
    {
        private readonly string _firstName;
        private readonly string _lastName;
        private readonly string _dateTime;
        private readonly string _vehicleTitle;
        private readonly string _expert;
        private readonly string _beverage;
        private readonly string _road;
        protected const string TemplateUrl = "https://generalstandart256.blob.core.windows.net/testdrive-email-templates/complete-booking__email-template.html";

        public CompleteBookingEmailTemplate(string firstName, string lastName, string dateTime, string vehicleTitle, string expert, string beverage, string road)
        {
            _firstName = firstName;
            _lastName = lastName;
            _dateTime = dateTime;
            _vehicleTitle = vehicleTitle;
            _expert = expert;
            _beverage = beverage;
            _road = road;
        }
        
        public string AsHtml()
        {
            // TODO: use method as async
            var html = new HttpClient().GetStringAsync(TemplateUrl).Result;

            html = html.Replace("{{first_name}}", _firstName);
            html = html.Replace("{{last_name}}", _lastName);
            html = html.Replace("{{dateTime}}", _dateTime);
            html = html.Replace("{{vehicle_title}}", _vehicleTitle);
            html = html.Replace("{{expert}}", _expert);
            html = html.Replace("{{beverage}}", _beverage);
            html = html.Replace("{{road}}", _road);

            return html;
        }

        public string AsPlainText()
        {
            // TODO: implement plain text email content
            return "";
        }
    }
}
