using System;
using System.Globalization;
using System.Net.Http;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Utils.Email.Data.Templates
{
    public class CompleteBookingEmailTemplate : IEmailTemplate
    {
        protected const string TemplateUrl = "https://generalstandart256.blob.core.windows.net/testdrive-email-templates/complete-booking__email-template.html";
        private readonly string _customerFirstName;
        private readonly string _customerLastName;
        private readonly string _customerComment;
        private DateTime? _bookingDateTime;
        private int _timeZoneOffset;
        private readonly string _vehicleImgUrl;
        private readonly string _vehicleTitle;
        private readonly string _vdpUrl;
        private readonly string _expertName;
        private readonly string _beverageName;
        private readonly string _roadName;
        private readonly string _dealerName;
        private readonly string _dealerAddress;
        private readonly string _dealerPhone;
        private readonly string _dealerSiteUrl;

        public CompleteBookingEmailTemplate(
            string customerFirstName,
            string customerLastName,
            DateTime? bookingDateTime,
            string vehicleImgUrl,
            string vehicleTitle,
            string vdpUrl,
            string expertName,
            string beverageName,
            string roadName,
            string dealerName,
            string dealerAddress,
            string dealerPhone,
            string dealerSiteUrl
            )
        {
            _vehicleTitle = vehicleTitle;
            _vdpUrl = vdpUrl;
            _vehicleImgUrl = vehicleImgUrl;
            _customerFirstName = customerFirstName;
            _customerLastName = customerLastName;
            _bookingDateTime = bookingDateTime;
            _expertName = expertName;
            _beverageName = beverageName;
            _roadName = roadName;
            _dealerName = dealerName;
            _dealerAddress = dealerAddress;
            _dealerPhone = dealerPhone;
            _dealerSiteUrl = dealerSiteUrl;
        }

        public CompleteBookingEmailTemplate(Lead lead, int timeZoneOffset = 0)
        {
            _vehicleTitle = lead.CarTitle;
            _vehicleImgUrl = lead.CarImageUrl;
            _vdpUrl = lead.VdpUrl;
            _customerFirstName = lead.FirstName;
            _customerLastName = lead.SecondName;
            _customerComment = lead.UserComment;
            _bookingDateTime = lead.BookingDateTimeUtc;
            _timeZoneOffset = timeZoneOffset;
            _expertName = (lead.Expert != null) ? lead.Expert.Name : "Skipped by customer";
            _beverageName = (lead.Beverage != null) ? lead.Beverage.Name : "Skipped by customer";
            _roadName = (lead.Route != null) ? lead.Route.Name : "Skipped by customer";
            _dealerName = lead.Site.DealerName;
            _dealerAddress = lead.Site.DealerAddress;
            _dealerPhone = lead.Site.DealerPhone;
            _dealerSiteUrl = lead.Site.Url;
        }

        public string AsHtml()
        {
            var html = new HttpClient().GetStringAsync((string) TemplateUrl).Result;

            #region Vehicle
            html = html.Replace("{{vehicleTitle}}", _vehicleTitle);
            html = html.Replace("{{vehicleImgUrl}}", _vehicleImgUrl);
            html = html.Replace("{{vdpUrl}}", _vdpUrl);
            #endregion

            #region Customer
            html = html.Replace("{{customerFirstName}}", _customerFirstName);
            html = html.Replace("{{customerLastName}}", _customerLastName);
            html = html.Replace("{{customerComment}}", _customerComment);
            #endregion

            #region Booking
            html = html.Replace("{{bookingDateTime}}", _bookingDateTime.GetValueOrDefault()
                .Add(new TimeSpan(0, -_timeZoneOffset, 0))
                .ToString(QuantumLogicConstants.UsaTimeFormat, CultureInfo.InvariantCulture));
            html = html.Replace("{{expertName}}", _expertName);
            html = html.Replace("{{beverageName}}", _beverageName);
            html = html.Replace("{{roadName}}", _roadName);
            #endregion

            #region Dealer info
            html = html.Replace("{{dealerName}}", _dealerName);
            html = html.Replace("{{dealerAddress}}", _dealerAddress);
            html = html.Replace("{{dealerPhone}}", _dealerPhone);
            html = html.Replace("{{dealerSiteUrl}}", _dealerSiteUrl);
            #endregion

            return html;
        }

        public string AsPlainText()
        {
            // TODO: implement plain text email content
            return "";
        }
    }
}
