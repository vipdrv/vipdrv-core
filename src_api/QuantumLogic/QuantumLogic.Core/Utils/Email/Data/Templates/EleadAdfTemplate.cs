using System;
using System.Globalization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Utils.Email.Data.Templates
{
    public class EleadAdfTemplate : IEmailTemplate
    {
        public DateTime? BookingDateTimeUtc { get; }
        public string CarTitle { get; }
        public string FirstName { get; }
        public string SecondName { get; }
        public string UserPhone { get; }
        public string UserEmail { get; }
        public string SiteName { get; }
        public string CarVin { get; }
        public string DealerName { get; }
        public string ExpertName { get; }
        public string BeverageName { get; }
        public string RouteTitle { get; }

        public EleadAdfTemplate(DateTime bookingDateTimeUtc, 
            string carTitle, 
            string firstName, 
            string secondName, 
            string userPhone, 
            string userEmail, 
            string siteName,
            string carVin,
            string dealerName,
            string expertName, 
            string beverageName, 
            string routeTitle)
        {
            BookingDateTimeUtc = bookingDateTimeUtc;
            CarTitle = carTitle;
            FirstName = firstName;
            SecondName = secondName;
            UserPhone = userPhone;
            UserEmail = userEmail;
            SiteName = siteName;
            CarVin = carVin;
            DealerName = dealerName;
            ExpertName = expertName;
            BeverageName = beverageName;
            RouteTitle = routeTitle;
        }

        public EleadAdfTemplate(Lead lead)
        {
            BookingDateTimeUtc = lead.BookingDateTimeUtc;
            CarVin = lead.CarVin;
            CarTitle = lead.CarTitle;
            FirstName = lead.FirstName;
            SecondName = lead.SecondName;
            UserPhone = lead.UserPhone;
            UserEmail = lead.UserEmail;
            SiteName = lead.Site.Name;
            DealerName = lead.Site.DealerName;
            ExpertName = (lead.Expert != null) ? lead.Expert.Name : "Skipped by customer";
            BeverageName = (lead.Beverage != null) ? lead.Beverage.Name : "Skipped by customer";
            RouteTitle = (lead.Route != null) ? lead.Route.Name : "Skipped by customer";
        }
        
        public string AsHtml()
        {
            var xml = $"<?xml version=\"1.0\">" +
                      $"<?adf version=\"1.0\"?>" +
                      $"<adf>" +
                          $"<prospect status=\"new\">" +
                              $"<id sequence=\"uniqueLeadId\" source=\"{SiteName}\"></id>" +
                              $"<requestdate>{DateTime.Now.ToString(QuantumLogicConstants.OutputDateTimeFormat, CultureInfo.InvariantCulture)}</requestdate>" +
                              $"<vehicle  interest=\"test-drive\">" +
                                  $"<vin>{CarVin}</vin>" +
                                  $"<title>{CarTitle}</title>" +
                               // $"<year>2008</year>" +
                               // $"<make>Make</make>" +
                               // $"<model>Model</model>" +
                              $"</vehicle>" +
                              $"<customer>" +
                                  $"<contact>" +
                                      $"<name part=\"first\">{FirstName}</name>" +
                                      $"<name part=\"last\">{SecondName}</name>" +
                                      $"<phone>{UserPhone}</phone>" +
                                      $"<email>{UserEmail}</email>" +
                                      $"<comments>" +
                                          $"Date & Time: {BookingDateTimeUtc.GetValueOrDefault().ToString(QuantumLogicConstants.UsaTimeFormat, CultureInfo.InvariantCulture)} " +
                                          $"Expert: {ExpertName} " +
                                          $"Beverage: {BeverageName} " +
                                          $"Route: {RouteTitle} " +
                                      $"</comments>" +
                                  $"</contact>" +
                              $"</customer>" +
                              $"<vendor>" +
                                  $"<contact>" +
                                      $"<name part=\"full\">{DealerName}</name>" +
                                  $"</contact>" +
                              $"</vendor>" +
                          $"</prospect>" +
                      $"</adf>";

            return xml;
        }

        public string AsPlainText()
        {
            return AsHtml();
        }
    }
}
