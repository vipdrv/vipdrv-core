using System;
using System.Globalization;
using System.Net.Http;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Utils.Email.Data.Templates
{
    public class NewLeadNotificationEmailTemplate : IEmailTemplate
    {
        protected const string TemplateUrl = "https://generalstandart256.blob.core.windows.net/testdrive-email-templates/new-lead__email-template.html";
        private readonly string _vehicleImgUrl;
        private readonly string _vdpUrl;
        private readonly string _VIN;
        private readonly string _customerFirstName;
        private readonly string _customerLastName;
        private readonly string _customerPhone;
        private readonly string _customerEmail;
        private readonly string _customerComment;
        private DateTime? _bookingDateTime;
        private int _timeZoneOffset;
        private readonly string _vehicleTitle;
        private readonly string _expertName;
        private readonly string _beverageName;
        private readonly string _roadName;

        public NewLeadNotificationEmailTemplate(
            string vehicleTitle,
            string vehicleImgUrl,
            string vdpUrl,
            string VIN,
            string customerFirstName,
            string customerLastName,
            string customerPhone,
            string customerEmail,
            DateTime bookingDateTime,
            string expertName,
            string beverageName,
            string roadName)
        {
            _vehicleImgUrl = vehicleImgUrl;
            _vdpUrl = vdpUrl;
            _VIN = VIN;
            _customerFirstName = customerFirstName;
            _customerLastName = customerLastName;
            _customerPhone = customerPhone;
            _customerEmail = customerEmail;
            _bookingDateTime = bookingDateTime;
            _vehicleTitle = vehicleTitle;
            _expertName = expertName;
            _beverageName = beverageName;
            _roadName = roadName;
        }

        public NewLeadNotificationEmailTemplate(Lead lead, int timeZoneOffset = 0)
        {
            _vehicleImgUrl = lead.CarImageUrl;
            _vdpUrl = lead.VdpUrl;
            _VIN = lead.CarVin;
            _customerFirstName = lead.FirstName;
            _customerLastName = lead.SecondName;
            _customerPhone = lead.UserPhone;
            _customerEmail = lead.UserEmail;
            _customerComment = lead.UserComment;
            _bookingDateTime = lead.BookingDateTimeUtc;
            _timeZoneOffset = timeZoneOffset;
            _vehicleTitle = lead.CarTitle;
            _expertName = (lead.Expert != null) ? lead.Expert.Name : "Skipped by customer";
            _beverageName = (lead.Beverage != null) ? lead.Beverage.Name : "Skipped by customer";
            _roadName = (lead.Route != null) ? lead.Route.Name : "Skipped by customer";
        }

        public string AsHtml()
        {
            // TODO: use method as async
            var html = new HttpClient().GetStringAsync((string)TemplateUrl).Result;

            html = html.Replace("{{vehicleTitle}}", _vehicleTitle);
            html = html.Replace("{{vehicleImgUrl}}", _vehicleImgUrl);
            html = html.Replace("{{VIN}}", _VIN);
            html = html.Replace("{{vdpUrl}}", _vdpUrl);

            html = html.Replace("{{customerFirstName}}", _customerFirstName);
            html = html.Replace("{{customerLastName}}", _customerLastName);
            html = html.Replace("{{customerPhone}}", _customerPhone);
            html = html.Replace("{{customerEmail}}", _customerEmail);
            html = html.Replace("{{customerComment}}", _customerComment);

            html = html.Replace("{{bookingDateTime}}", _bookingDateTime.GetValueOrDefault()
                .Add(new TimeSpan(0, -_timeZoneOffset, 0))
                .ToString(QuantumLogicConstants.UsaTimeFormat, CultureInfo.InvariantCulture));
            html = html.Replace("{{expertName}}", _expertName);
            html = html.Replace("{{beverageName}}", _beverageName);
            html = html.Replace("{{roadName}}", _roadName);

            return html;
        }

        public string AsPlainText()
        {
            return "";
        }
    }
}
