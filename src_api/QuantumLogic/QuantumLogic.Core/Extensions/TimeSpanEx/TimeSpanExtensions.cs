using System;

namespace QuantumLogic.Core.Extensions
{
    public static partial class Extensions
    {
        public static string FormatTimeSpanToUserFriendlyShortString(this TimeSpan ts)
        {
            if (ts.Days >= 1)
            {
                return ts.ToString(Constants.QuantumLogicConstants.ShortTimeSpanFormatWithDays);
            }
            else
            {
                return ts.ToString(Constants.QuantumLogicConstants.ShortTimeSpanFormatWithoutDays);
            }
        }

        public static string FormatTimeSpanToUserFriendlyLongString(this TimeSpan ts)
        {
            if (ts.Days >= 1)
            {
                return ts.ToString(Constants.QuantumLogicConstants.LongTimeSpanFormatWithDays);
            }
            else
            {
                return ts.ToString(Constants.QuantumLogicConstants.LongTimeSpanFormatWithoutDays);
            }
        }
    }
}
