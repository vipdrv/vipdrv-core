using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Scheduling.Week;
using System;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Experts
{
    public class ExpertFullDto : ExpertDto
    {
        public IList<DayOfWeekInterval> WorkingHours { get; set; }
        public string UrlToSeparatedPage { get; set; }

        public override void MapFromEntity(Expert entity)
        {
            base.MapFromEntity(entity);
            UrlToSeparatedPage = String.IsNullOrWhiteSpace(entity.Site.WidgetAsSeparatePageUrl) ? null :
                $"{entity.Site.WidgetAsSeparatePageUrl}/#expertId={Id}";
            WorkingHours = DayOfWeekInterval.Parse(entity.WorkingHours);
        }
        public override Expert MapToEntity()
        {
            Expert expert = base.MapToEntity();
            expert.WorkingHours = String.Join(DayOfWeekInterval.DayOfWeekIntervalsSeparator.ToString(), DayOfWeekInterval.Purify(WorkingHours).Select(r => r.ToString()));
            return expert;
        }
    }
}
