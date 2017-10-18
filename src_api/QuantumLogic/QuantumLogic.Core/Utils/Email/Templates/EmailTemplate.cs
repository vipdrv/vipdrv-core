using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;

namespace QuantumLogic.Core.Utils.Email.Templates
{
    public class EmailTemplate 
    {
        private readonly string _completeBookingEmailTemplateUrl;
        private readonly string _newLeadNotificationEmailTemplate;
        private readonly string _dealerInvitationEmailTemplate;

        public EmailTemplate()
        {
            _completeBookingEmailTemplateUrl = "https://generalstandart256.blob.core.windows.net/testdrive-email-templates/complete-booking__email-template.html";
            _newLeadNotificationEmailTemplate = "https://generalstandart256.blob.core.windows.net/testdrive-email-templates/new-lead__email-template.html";
            _dealerInvitationEmailTemplate = "https://generalstandart256.blob.core.windows.net/testdrive-email-templates/dealer-invitation__email-template.html";
        }
        public string GetTestDriveEmailTemplate(string emailContent)
        {
            return emailContent;
        }

        public string GetBookingEmailTemplate(string firstName, string lastName, string dateTime, string vehicleTitle, string expert, string beverage, string road)
        {
            var html = new HttpClient().GetStringAsync(_completeBookingEmailTemplateUrl).Result;

            html = html.Replace("{{first_name}}", firstName);
            html = html.Replace("{{last_name}}", lastName);
            html = html.Replace("{{dateTime}}", dateTime);
            html = html.Replace("{{vehicle_title}}", vehicleTitle);
            html = html.Replace("{{expert}}", expert);
            html = html.Replace("{{beverage}}", beverage);
            html = html.Replace("{{road}}", road);

            return html;
        }

        public string GetDealerInvitationEmailTemplate(string invitationLink)
        {
            var html = new HttpClient().GetStringAsync(_dealerInvitationEmailTemplate).Result;

            html = html.Replace("{{invitationLink}}", invitationLink);

            return html;
        }

        public string GetDealerInvitationEmailTemplate(string firstName, string lastName, string dateTime, string vehicleTitle,
            string expert, string beverage, string road)
        {

            // TODO: complete implementation
            return "";
        }
    }
}
