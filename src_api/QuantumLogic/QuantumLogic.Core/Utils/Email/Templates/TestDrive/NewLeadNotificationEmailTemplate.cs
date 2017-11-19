﻿using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;
using QuantumLogic.Core.Constants;

namespace QuantumLogic.Core.Utils.Email.Templates.TestDrive
{
    public class NewLeadNotificationEmailTemplate : IEmailTemplate
    {
        protected const string TemplateUrl = "https://generalstandart256.blob.core.windows.net/testdrive-email-templates/new-lead__email-template.html";
        private readonly string _vehicleImgUrl;
        private readonly string _VIN;
        private readonly string _customerFirstName;
        private readonly string _customerLastName;
        private readonly string _customerPhone;
        private readonly string _customerEmail;
        private readonly string _bookingDateTime;
        private readonly string _vehicleTitle;
        private readonly string _expertName;
        private readonly string _beverageName;
        private readonly string _roadName;

        public NewLeadNotificationEmailTemplate(
            string vehicleTitle,
            string vehicleImgUrl,
            string VIN,
            string customerFirstName,
            string customerLastName,
            string customerPhone,
            string customerEmail,
            string bookingDateTime, 
            string expertName, 
            string beverageName,
            string roadName)
        {
            _vehicleImgUrl = vehicleImgUrl;
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

        public string AsHtml()
        {
            // TODO: use method as async
            var html = new HttpClient().GetStringAsync(TemplateUrl).Result;

            html = html.Replace("{{vehicleTitle}}", _vehicleTitle);
            html = html.Replace("{{vehicleImgUrl}}", _vehicleImgUrl);
            html = html.Replace("{{VIN}}", _VIN);

            html = html.Replace("{{customerFirstName}}", _customerFirstName);
            html = html.Replace("{{customerLastName}}", _customerLastName);
            html = html.Replace("{{customerPhone}}", _customerPhone);
            html = html.Replace("{{customerEmail}}", _customerEmail);

            html = html.Replace("{{bookingDateTime}}", _bookingDateTime);
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