using System;
using System.Globalization;
using System.Text.RegularExpressions;
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
        public string UserComments { get; }
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
            string userComments,
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
            UserComments = userComments;
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
            UserComments = lead.UserComment;
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
                price = Regex.Replace(vehicle.Msrp, "[^0-9]", "");
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

        protected string BookingDataTxt(string bookingDateTime, string expertName, string beverageName, string routeName, string userComments)
        {
            string bookingDateTimeNode = !String.IsNullOrEmpty(bookingDateTime) ? $"Test Drive DateTime: {bookingDateTime};  " : "";
            string expertNameNode = !String.IsNullOrEmpty(expertName) ? $"Sales Person: {expertName};  \n" : "";
            string beverageNameNode = !String.IsNullOrEmpty(beverageName) ? $"Beverage: {beverageName};  \n" : "";
            string routeNameNode = !String.IsNullOrEmpty(routeName) ? $"Route: {routeName};  \n" : "";
            string userCommentsNode = !String.IsNullOrEmpty(userComments) ? $"User Comments: {userComments};  \n" : "";

            string result = bookingDateTimeNode +
                            expertNameNode +
                            beverageNameNode +
                            routeNameNode +
                            userCommentsNode;

            return result;
        }

        public string AsHtml()
        {
            string recieveDateTime = DateTime.UtcNow
                .Add(new TimeSpan(0, -TimeZoneOffset, 0))
                .ToString(QuantumLogicConstants.OutputDateTimeFormat, CultureInfo.InvariantCulture);

            string bookingDateTime = "Skipped by customer";
            if (BookingDateTimeUtc != null)
            {
                bookingDateTime = BookingDateTimeUtc.GetValueOrDefault()
                    .Add(new TimeSpan(0, -TimeZoneOffset, 0))
                    .ToString(QuantumLogicConstants.UsaTimeFormat, CultureInfo.InvariantCulture);
            }

            string vehicleXmlNode = VehicleXmlNode(Vehicle);
            string bookingDataTxt = BookingDataTxt(bookingDateTime, ExpertName, BeverageName, RouteTitle, UserComments);

            var xml = $"<?xml version=\"1.0\" encoding=\"UTF-8\"?>" +
                      $"<?adf version=\"1.0\"?>" +
                      $"<adf>" +
                          $"<prospect status=\"new\">" +
                              $"<id sequence=\"{DealerName}\" source=\"{SiteName}\"></id>" +
                                //$"<requestdate>{recieveDateTime}</requestdate>" +
                                vehicleXmlNode +
                              $"<customer>" +
                                  $"<contact>" +
                                      $"<name part=\"first\">{FirstName}</name>" +
                                      $"<name part=\"last\">{SecondName}</name>" +
                                      $"<phone type=\"cellphone\">{UserPhone}</phone>" +
                                      $"<email>{UserEmail}</email>" +
                                  $"</contact>" +
                                  $"<comments>" +
                                    bookingDataTxt +
                                  $"</comments>" +
                              $"</customer>" +
                              $"<vendorname>" +
                                $"{DealerName} [Attn: {ExpertName}]" +
                              $"</vendorname>" + 
                              $"<vendor>" +
                                  $"<contact>" +
                                      $"<name part=\"full\">{DealerName}</name>" +
                                  $"</contact>" +
                              $"</vendor>" +
                              $"<provider>" +
                                $"<name>VIPdrv Test Drive</name>" + 
                              $"</provider>" +
                              $"<salesperson>" +
                                $"<id source=\"DealerPeak\">{DealerPeakSalesId}</id>" +
                              $"</salesperson>" +
                              $"<agent>" + 
                                $"{DealerPeakSalesId}" +
                              $"</agent>" +
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
