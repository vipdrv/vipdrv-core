using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.ADFGenerator
{
    class DefaultAdf
    {
        private readonly string _bookingDate;
        private readonly string _vehicleTitle;
        private readonly string _vehicleYear;

        public DefaultAdf(string bookingDate, string vehicleTitle, string vehicleYear)
        {
            _bookingDate = bookingDate;
            _vehicleTitle = vehicleTitle;
            _vehicleYear = vehicleYear;
        }

        public string AsString()
        {
            var xml =
                    $"<?xml version=\"1.0\" encoding=\"UTF-8\"?>" +
                    $"<?adf version=\"1.0\"?>" +
                    $"<adf>" +
                        $"<prospect>" +
                            $"<requestdate>{_bookingDate}</requestdate>" +
                            $"<vehicle>" +
                                $"<year>2008</year>" +
                                $"<make>Make</make>" +
                                $"<model>Model</model>" +
                            $"</vehicle>" +
                            $"<customer>" +
                                $"<contact>" +
                                    $"<name part=\"first\">First</name>" +
                                    $"<name part=\"last\">Last</name>" +
                                    $"<phone>323-223-3322</phone>" +
                                    $"<email>emailaddress</email>" +
                                $"</contact>" +
                            $"</customer>" +
                            $"<vendor>" +
                                $"<contact>" +
                                    $"<name part=\"full\">Dealer Name</name>" +
                                $"</contact>" +
                            $"</vendor>" +
                        $"</prospect>" +
                    $"</adf>";
            return xml;
        }
        
    }
}
