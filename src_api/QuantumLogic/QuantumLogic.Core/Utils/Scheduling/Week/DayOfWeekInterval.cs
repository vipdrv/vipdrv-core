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
        #region Consts

        /// <summary>
        /// Separator between interval parts
        /// </summary>
        public const char DayOfWeekIntervalSectionsSeparator = ',';
        /// <summary>
        /// Separator between intervals
        /// </summary>
        public const char DayOfWeekIntervalsSeparator = ';';

        #endregion

        public DayOfWeek DayOfWeek { get; set; }
        public TimeSpan StartTime { get; set; }
        public TimeSpan EndTime { get; set; }

        #region Ctors

        public DayOfWeekInterval() { }

        public DayOfWeekInterval(string str)
        {
            IList<string> splitStr = str.Split(DayOfWeekIntervalSectionsSeparator);
            if (splitStr.Count == 3)
            {
                try
                {
                    DayOfWeek = (DayOfWeek)Int32.Parse(splitStr[0]);
                    StartTime = TimeSpan.Parse(splitStr[1]);
                    EndTime = TimeSpan.Parse(splitStr[2]);
                    if (StartTime >= EndTime)
                    {
                        throw new ArgumentException($"{nameof(StartTime)} have to be early then {nameof(EndTime)} for interval \"{str}\".");
                    }
                }
                catch (Exception ex)
                {
                    throw new ArgumentException($"Initialization failed for WeekScheduleInterval: \"{str}\"! ({ex})");
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
            if (StartTime >= EndTime)
            {
                throw new ArgumentException($"{nameof(StartTime)} ({StartTime}) have to be early then {nameof(EndTime)} (({EndTime})) for interval.");
            }
        }

        #endregion

        public override string ToString()
        {
            return $"{(int)DayOfWeek},{StartTime},{EndTime}";
        }

        /// <summary>
        /// Is used to parse string with day of week intervals
        /// </summary>
        /// <param name="str">string</param>
        /// <returns>day of week intervals</returns>
        public static IList<DayOfWeekInterval> Parse(string str)
        {
            return String.IsNullOrEmpty(str) ?
                new List<DayOfWeekInterval>() :
                str.Split(DayOfWeekIntervalsSeparator)
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
            else if (intervals.Count < 2)
            {
                return intervals;
            } 
            IList<DayOfWeekInterval> pureIntervals = new List<DayOfWeekInterval>();
            IList<DayOfWeekInterval> sortedIntervals = intervals.OrderBy(r => r.DayOfWeek).ThenBy(r => r.StartTime).ToList();
            DayOfWeek currentDayOfWeek = sortedIntervals.First().DayOfWeek;
            TimeSpan currentStartTime = sortedIntervals.First().StartTime;
            TimeSpan currentEndTime = sortedIntervals.First().EndTime;
            for (int i = 1; i < sortedIntervals.Count; i++)
            {
                if (sortedIntervals[i].DayOfWeek == currentDayOfWeek && sortedIntervals[i].StartTime <= currentEndTime && sortedIntervals[i].EndTime > currentEndTime)
                {
                    // prolong current interval
                    currentEndTime = sortedIntervals[i].EndTime;
                }
                else if (sortedIntervals[i].DayOfWeek == currentDayOfWeek && sortedIntervals[i].StartTime <= currentEndTime)
                {
                    // doing nothing cuz current interval contains new one
                }
                else
                {
                    // add new pure interval and start new one
                    pureIntervals.Add(new DayOfWeekInterval(currentDayOfWeek, currentStartTime, currentEndTime));
                    currentDayOfWeek = sortedIntervals[i].DayOfWeek;
                    currentStartTime = sortedIntervals[i].StartTime;
                    currentEndTime = sortedIntervals[i].EndTime;
                }
            }
            pureIntervals.Add(new DayOfWeekInterval(currentDayOfWeek, currentStartTime, currentEndTime));
            return pureIntervals;
        }
    }
}
