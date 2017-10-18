using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.Email.Templates
{
    public interface ICompleteBookingEmailTemplate
    {
        /// <summary>
        /// Is used to create CompleteBooking Email Template
        /// </summary>
        /// <param name="firstName"></param>
        /// <param name="lastName"></param>
        /// <param name="dateTime"></param>
        /// <param name="vehicleTitle"></param>
        /// <param name="expert"></param>
        /// <param name="beverage"></param>
        /// <param name="road"></param>
        /// <returns>
        /// HTML template as a string
        /// </returns>
        string GetBookingEmailTemplate(string firstName, string lastName, string dateTime, string vehicleTitle,
            string expert, string beverage, string road);
    }
}
