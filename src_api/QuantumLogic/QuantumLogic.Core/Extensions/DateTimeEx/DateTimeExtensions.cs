using System;

namespace QuantumLogic.Core.Extensions.DateTimeEx
{
    public static partial class Extensions
    {
        public static string FormatUtcDateTimeToUserFriendlyString(this DateTime dt, TimeSpan timeZoneOffset, string format = "G")
        {
            return dt.Add(timeZoneOffset).ToString(format);
        }
    }
}
