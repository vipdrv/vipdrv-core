using QuantumLogic.Core.Utils.Scheduling.Week;
using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Responses.Widget.Site
{
    public class SiteWeekSchedule
    {
        public int SiteId { get; private set; }
        public IList<DayOfWeekInterval> workingIntervals { get; private set; }

        #region Ctors

        public SiteWeekSchedule(int siteId, IList<DayOfWeekInterval> workingIntervals)
        {
            SiteId = siteId;
            this.workingIntervals = workingIntervals;
        }

        #endregion
    }
}
