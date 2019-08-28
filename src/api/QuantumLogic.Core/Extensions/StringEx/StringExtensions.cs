using System;
using System.Linq;

namespace QuantumLogic.Core.Extensions
{
    public static partial class Extensions
    {
        /// <summary>
        /// Is used to return String.Empty if value is null
        /// </summary>
        /// <param name="value">string value</param>
        /// <returns>value or String.Empty</returns>
        public static string EmptyIfNull(this string value)
        {
            return value ?? String.Empty;
        }
        /// <summary>
        /// Is used check value on empty or white space and returns null if it is
        /// </summary>
        /// <param name="value">string value</param>
        /// <returns>value or null</returns>
        public static string NullIfEmptyOrWhiteSpace(this string value)
        {
            return String.IsNullOrWhiteSpace(value) || value.Length == 0 ? null : value;
        }
        /// <summary>
        /// Is used to create new string that is value after operations 
        /// trim (remove start and end spaces) from string and after this 
        /// set new string to null if it is string with length 0
        /// </summary>
        /// <param name="value">string value</param>
        /// <returns>new string with modifications</returns>
        public static string TrimAndNullIfEmpty(this string value)
        {
            return value == null ? null : value.Trim().NullIfEmptyOrWhiteSpace();
        }
        /// <summary>
        /// Is used to underscore string
        /// </summary>
        /// <param name="str">string</param>
        /// <returns>underscore string</returns>
        public static string ToUnderscoreCase(this string str)
        {
            return String
                .Concat(str.Select((x, i) => char.IsUpper(x) ? "_" + x.ToString() : x.ToString()))
                .ToLower();
        }
        /// <summary>
        /// Is used to upperscore string
        /// </summary>
        /// <param name="str">string</param>
        /// <returns>upperscore string</returns>
        public static string ToUpperscoreCase(this string str)
        {
            return String
                .Concat(str.Select((x, i) => char.IsUpper(x) ? "_" + x.ToString() : x.ToString()))
                .ToUpper();
        }
    }
}
