using System;
using System.Globalization;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Email.Data.Templates.Arguments;

namespace QuantumLogic.Core.Utils.Email.Data.Templates
{
    public class EleadAdfTemplate : IEmailTemplate
    {
        public DateTime? BookingDateTimeUtc { get; }
        public int TimeZoneOffset { get; }
        public string FirstName { get; }
        public string SecondName { get; }
        public string UserPhone { get; }
        public string UserEmail { get; }
        public string SiteName { get; }
        public string DealerName { get; }
        public string ExpertName { get; }
        public string BeverageName { get; }
        public string RouteTitle { get; }
        public string DealerPeakSalesId { get; }
        public IVehicle Vehicle { get; }

        public EleadAdfTemplate(
            DateTime bookingDateTimeUtc,
            int timeZoneOffset,
            string firstName,
            string secondName,
            string userPhone,
            string userEmail,
            string siteName,
            string dealerName,
            string expertName,
            string beverageName,
            string routeTitle,
            string dealerPeakSalesId,
            IVehicle vehicle)
        {
            BookingDateTimeUtc = bookingDateTimeUtc;
            TimeZoneOffset = timeZoneOffset;
            FirstName = firstName;
            SecondName = secondName;
            UserPhone = userPhone;
            UserEmail = userEmail;
            SiteName = siteName;
            DealerName = dealerName;
            ExpertName = expertName;
            BeverageName = beverageName;
            RouteTitle = routeTitle;
            DealerPeakSalesId = dealerPeakSalesId;
            Vehicle = vehicle;
        }

        public EleadAdfTemplate(Lead lead, IVehicle vehicle, int timeZoneOffset = 0)
        {
            BookingDateTimeUtc = lead.BookingDateTimeUtc;
            TimeZoneOffset = timeZoneOffset;
            FirstName = lead.FirstName;
            SecondName = lead.SecondName;
            UserPhone = lead.UserPhone;
            UserEmail = lead.UserEmail;
            SiteName = lead.Site?.Name;
            DealerName = lead.Site?.DealerName;
            ExpertName = (lead.Expert != null) ? lead.Expert.Name : "Skipped by customer";
            BeverageName = (lead.Beverage != null) ? lead.Beverage.Name : "Skipped by customer";
            RouteTitle = (lead.Route != null) ? lead.Route.Name : "Skipped by customer";
            DealerPeakSalesId = (lead.Expert != null) ? lead.Expert.EmployeeId : String.Empty;
            Vehicle = vehicle;
        }

        protected string VehiclePriceNode(IVehicle vehicle)
        {
            // < !-- numeric, e.g. 19500 --> <!-- type:quote|offer|msrp|invoice|call|appraisal|asking; currency:ISO 4217 3-letter code; delta:absolute|relative|percentage; relativeto:msrp|invoice; source:free text; -->

            string type = "";
            string price = "";
            string currency = "USD";
            if (!String.IsNullOrWhiteSpace(vehicle.Msrp))
            {
                type = "msrp";
                price = vehicle.Msrp;
            }

            return $"<price type=\"{type}\" currency=\"{currency}\" delta=\"\" relativeto=\"\" source=\"\">{price}</price>";
        }

        protected string VehicleXmlNode(IVehicle vehicle)
        {
            string vehiclePriceXmlNode = VehiclePriceNode(vehicle);
            string xml = $"<vehicle interest=\"test-drive\" status=\"{vehicle.Condition}\">" + // < !-- interest:buy|lease|sell|trade-in|test-drive; status:new|used; -->
                             $"<vin>{vehicle.Vin}</vin>" +
                             $"<year>{vehicle.Year}</year>" +
                             $"<make>{vehicle.Make}</make>" +
                             $"<model>{vehicle.Model}</model>" +
                             $"<stock>{vehicle.Stock}</stock>" +
                             $"<trim></trim>" +
                             $"<odometer status=\"\" units=\"\"></odometer>" + // < !-- status:unknown|rolledover|replaced|original; units:km|mi; -->
                             $"<colorcombination>" +
                                $"<interiorcolor>{vehicle.Interior}</interiorcolor>" +
                                $"<exteriorcolor>{vehicle.Exterior}</exteriorcolor>" +
                                $"<preference></preference>" + // < !-- 1-n -->
                             $"</colorcombination>" +
                              vehiclePriceXmlNode +
                         $"</vehicle>";

            return xml;
        }

        protected string BookingDataTxt(string bookingDateTime, string expertName, string beverageName, string routeName)
        {
            string bookingDateTimeNode = !String.IsNullOrEmpty(bookingDateTime) ? $"Test Drive DateTime: {bookingDateTime}; " : "";
            string expertNameNode = !String.IsNullOrEmpty(expertName) ? $"Sales Person: {expertName}; " : "";
            string beverageNameNode = !String.IsNullOrEmpty(beverageName) ? $"Beverage: {beverageName}; " : "";
            string routeNameNode = !String.IsNullOrEmpty(routeName) ? $"Route: {routeName}; " : "";

            string result = bookingDateTimeNode +
                            expertNameNode +
                            beverageNameNode +
                            routeNameNode;

            return result;
        }

        public string AsHtml()
        {
            string recieveDateTime = DateTime.UtcNow
                .Add(new TimeSpan(0, -TimeZoneOffset, 0))
                .ToString(QuantumLogicConstants.OutputDateTimeFormat, CultureInfo.InvariantCulture);

            string bookingDateTime = BookingDateTimeUtc.GetValueOrDefault()
                .Add(new TimeSpan(0, -TimeZoneOffset, 0))
                .ToString(QuantumLogicConstants.UsaTimeFormat, CultureInfo.InvariantCulture);

            string vehicleXmlNode = VehicleXmlNode(Vehicle);
            string bookingDataTxt = BookingDataTxt(bookingDateTime, ExpertName, BeverageName, RouteTitle);

            var xml = $"<?xml version=\"1.0\" encoding=\"UTF-8\"?>" +
                      $"<?adf version=\"1.0\"?>" +
                      $"<adf>" +
                          $"<prospect status=\"new\">" +
                              $"<id sequence=\"{DealerName}\" source=\"{SiteName}\"></id>" +
                              $"<requestdate>{recieveDateTime}</requestdate>" +
                                vehicleXmlNode +
                              $"<salesperson>" +
                                  $"<id source=\"DealerPeak\">{DealerPeakSalesId}</id>" +
                              $"</salesperson>" +
                              $"<customer>" +
                                  $"<contact>" +
                                      $"<name part=\"first\">{FirstName}</name>" +
                                      $"<name part=\"last\">{SecondName}</name>" +
                                      $"<phone>{UserPhone}</phone>" +
                                      $"<email>{UserEmail}</email>" +
                                      $"<comments>" +
                                            bookingDataTxt +
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
