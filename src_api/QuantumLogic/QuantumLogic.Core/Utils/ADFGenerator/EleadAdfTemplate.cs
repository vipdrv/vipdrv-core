using System;
using System.Collections.Generic;
using System.Globalization;
using System.Text;
using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Email;

namespace QuantumLogic.Core.Utils.ADFGenerator
{
    public class EleadAdfTemplate : IAdfTemplate
    {
        private readonly string _bookingDate;
        private readonly string _vehicleTitle;
        private readonly string _vehicleYear;

        public EleadAdfTemplate(string bookingDate, string vehicleTitle, string vehicleYear)
        {
            _bookingDate = bookingDate;
            _vehicleTitle = vehicleTitle;
            _vehicleYear = vehicleYear;
        }

        public EleadAdfTemplate(Lead lead)
        {
            _vehicleTitle = lead.CarTitle;
            _bookingDate = lead.BookingDateTimeUtc.GetValueOrDefault().ToString(QuantumLogicConstants.UsaTimeFormat, CultureInfo.InvariantCulture);
        }

        public string AsBase64()
        {
            byte[] encodedBytes = System.Text.Encoding.Unicode.GetBytes(AsString());

            return Convert.ToBase64String(encodedBytes);
        }

        public string AsString()
        {
            var xml =
                    $"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" +
                    $"<?adf version=\"1.0\"?>\n" +
                    $"<adf>\n" +
                        $"<prospect>\n" +
                            $"<requestdate>{_bookingDate}</requestdate>\n" +
                            $"<vehicle>\n" +
                                $"<year>2008</year>\n" +
                                $"<make>Make</make>\n" +
                                $"<model>Model</model>\n" +
                            $"</vehicle>\n" +
                            $"<customer>\n" +
                                $"<contact>\n" +
                                    $"<name part=\"first\">First</name>\n" +
                                    $"<name part=\"last\">Last</name>\n" +
                                    $"<phone>323-223-3322</phone>\n" +
                                    $"<email>emailaddress</email>\n" +
                                $"</contact>\n" +
                            $"</customer>\n" +
                            $"<vendor>\n" +
                                $"<contact>\n" +
                                    $"<name part=\"full\">Dealer Name</name>\n" +
                                $"</contact>\n" +
                            $"</vendor>\n" +
                        $"</prospect>\n" +
                    $"</adf>\n";
            return xml;
        }

    }
}
