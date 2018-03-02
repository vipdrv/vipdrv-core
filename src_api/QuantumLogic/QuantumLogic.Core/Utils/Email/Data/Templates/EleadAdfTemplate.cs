using System;
using System.Globalization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;

namespace QuantumLogic.Core.Utils.Email.Data.Templates
{
    public class EleadAdfTemplate : IEmailTemplate
    {
        public DateTime? BookingDateTimeUtc { get; }
        public int TimeZoneOffset { get; }
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
        public string DealerPeakSalesId { get; }

        public EleadAdfTemplate(DateTime bookingDateTimeUtc,
            int timeZoneOffset,
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
            TimeZoneOffset = timeZoneOffset;
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

        public EleadAdfTemplate(Lead lead, int timeZoneOffset= 0) //TODO: move optional parameter to lead entity
        {
            BookingDateTimeUtc = lead.BookingDateTimeUtc;
            TimeZoneOffset = timeZoneOffset;
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
            DealerPeakSalesId = (lead.Expert != null) ? lead.Expert.EmployeeId : String.Empty;
        }

        public string AsHtml()
        {
            string recieveDateTime = DateTime.UtcNow
                .Add(new TimeSpan(0, -TimeZoneOffset, 0))
                .ToString(QuantumLogicConstants.OutputDateTimeFormat, CultureInfo.InvariantCulture);

            string bookingDateTime = BookingDateTimeUtc.GetValueOrDefault()
                .Add(new TimeSpan(0, -TimeZoneOffset, 0))
                .ToString(QuantumLogicConstants.UsaTimeFormat, CultureInfo.InvariantCulture);

            var xml = $"<?xml version=\"1.0\" encoding=\"UTF-8\"?>" +
                      $"<?adf version=\"1.0\"?>" +
                      $"<adf>" +
                          $"<prospect>" + // status=\"new\"
                              $"<id sequence=\"{DealerName}\" source=\"{SiteName}\"></id>" +
                              $"<requestdate>{recieveDateTime}</requestdate>" +
                              $"<vehicle  interest=\"test-drive\">" +
                                  $"<vin>{CarVin}</vin>" +
                                  $"<title>{CarTitle}</title>" +
                              // $"<year>2008</year>" +
                              // $"<make>Make</make>" +
                              // $"<model>Model</model>" +
                              $"</vehicle>" +
                              $"<salesperson>" +
                                $"<id source=\"DealerPeak\">{DealerPeakSalesId}</id>" + //TODO: make XML Node optional
                              $"</salesperson>" +
                              $"<customer>" +
                                  $"<contact>" +
                                      $"<name part=\"first\">{FirstName}</name>" +
                                      $"<name part=\"last\">{SecondName}</name>" +
                                      $"<phone>{UserPhone}</phone>" +
                                      $"<email>{UserEmail}</email>" +
                                      $"<comments>" +
                                          $"Date and Time: {bookingDateTime} " +
                                          $"Sales Person: {ExpertName} " +
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
