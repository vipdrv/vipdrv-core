using System;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.Core.Utils.Scheduling.Week
{
    /// <summary>
    /// Is used like schedule interval for day of week
    /// </summary>
    public class DayOfWeekInterval
    {
        public DayOfWeek DayOfWeek { get; private set; }
        public TimeSpan StartTime { get; private set; }
        public TimeSpan EndTime { get; private set; }

        #region Ctors

        public DayOfWeekInterval(string str, char sectionSeparator = ',')
        {
            IList<string> splitStr = str.Split(sectionSeparator);
            if (splitStr.Count == 3)
            {
                try
                {
                    DayOfWeek = (DayOfWeek)Int32.Parse(splitStr[0]);
                    StartTime = TimeSpan.Parse(splitStr[1]);
                    EndTime = TimeSpan.Parse(splitStr[2]);
                }
                catch (Exception ex)
                {
                    throw new NotSupportedException($"Initialization failed for WeekScheduleInterval: \"{str}\"! ({ex})");
                }
            }
            else
            {
                throw new ArgumentException($"Argument {nameof(str)} is not valid (can not parse \"{str}\").");
            }
        }

        public DayOfWeekInterval(DayOfWeek dayOfWeek, TimeSpan startTimeOfDay, TimeSpan endTimeOfDay)
        {
            DayOfWeek = dayOfWeek;
            StartTime = startTimeOfDay;
            EndTime = endTimeOfDay;
        }

        #endregion

        public override string ToString()
        {
            return $"{DayOfWeek},{StartTime},{EndTime}";
        }

        /// <summary>
        /// Is used to parse string with day of week intervals
        /// </summary>
        /// <param name="str">string</param>
        /// <returns>day of week intervals</returns>
        public static IList<DayOfWeekInterval> Parse(string str, char sectionSeparator = ';')
        {
            return String.IsNullOrEmpty(str) ?
                new List<DayOfWeekInterval>() :
                str.Split(sectionSeparator)
                    .Where(r => !String.IsNullOrEmpty(r))
                    .Select(r => new DayOfWeekInterval(r))
                    .ToList();
        }

        /// <summary>
        /// Is used to merge day of week intervals: there should be no overlapping intervals after this method
        /// </summary>
        /// <param name="intervals">dirty intervals</param>
        /// <returns>pure intervals</returns>
        public static IList<DayOfWeekInterval> Purify(IList<DayOfWeekInterval> intervals)
        {
            if (intervals == null)
            {
                throw new ArgumentException($"Argument {nameof(intervals)} can not be null!");
            }
#warning: TODO: implement this
            return intervals;
        }
    }
}
