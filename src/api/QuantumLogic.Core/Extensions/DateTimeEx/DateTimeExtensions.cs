using System;

namespace QuantumLogic.Core.Extensions
{
    public static partial class Extensions
    {
        public static string FormatUtcDateTimeToUserFriendlyString(this DateTime dt, string format = "G")
        {
            return dt.ToString(format);
        }

        public static string FormatUtcDateTimeToUserFriendlyString(this DateTime dt, TimeSpan timeZoneOffset, string format = "G")
        {
            return dt.Add(timeZoneOffset).FormatUtcDateTimeToUserFriendlyString(format);
        }
    }
}
