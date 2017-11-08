using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;
using QuantumLogic.Core.Constants;

namespace QuantumLogic.Core.Utils.Email.Templates.TestDrive
{
    public class CompleteBookingEmailTemplate : IEmailTemplate
    {
        protected const string TemplateUrl = "https://generalstandart256.blob.core.windows.net/testdrive-email-templates/complete-booking__email-template.html";
        private readonly string _customerFirstName;
        private readonly string _customerLastName;
        private readonly string _bookingDateTime;
        private readonly string _vehicleImgUrl;
        private readonly string _vehicleTitle;
        private readonly string _expertName;
        private readonly string _beverageName;
        private readonly string _roadName;

        public CompleteBookingEmailTemplate(
            string customerFirstName,
            string customerLastName,
            string bookingDateTime,
            string vehicleImgUrl,
            string vehicleTitle,
            string expertName,
            string beverageName,
            string roadName)
        {
            _vehicleTitle = vehicleTitle;
            _vehicleImgUrl = vehicleImgUrl;
            _customerFirstName = customerFirstName;
            _customerLastName = customerLastName;
            _bookingDateTime = bookingDateTime;
            _expertName = expertName;
            _beverageName = beverageName;
            _roadName = roadName;
        }

        public string AsHtml()
        {
            // TODO: use method as async
            var html = new HttpClient().GetStringAsync(TemplateUrl).Result;

            html = html.Replace("{{vehicleTitle}}", _vehicleTitle);
            html = html.Replace("{{vehicleImgUrl}}", _vehicleImgUrl);

            html = html.Replace("{{customerFirstName}}", _customerFirstName);
            html = html.Replace("{{customerLastName}}", _customerLastName);

            html = html.Replace("{{bookingDateTime}}", _bookingDateTime);
            html = html.Replace("{{expertName}}", _expertName);
            html = html.Replace("{{beverageName}}", _beverageName);
            html = html.Replace("{{roadName}}", _roadName);

            return html;
        }

        public string AsPlainText()
        {
            // TODO: implement plain text email content
            return "";
        }
    }
}
