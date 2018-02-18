using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Scheduling.Week;
using System;
using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Sites
{
    public class SiteFullDto : SiteDto
    {
        public int ExpertsAmount { get; set; }
        public int BeveragesAmount { get; set; }
        public int RoutesAmount { get; set; }

        public int ActiveExpertsAmount { get; set; }
        public int ActiveBeveragesAmount { get; set; }
        public int ActiveRoutesAmount { get; set; }

        public int BeverageStepOrder { get; set; }
        public int ExpertStepOrder { get; set; }
        public int RouteStepOrder { get; set; }

        public IList<DayOfWeekInterval> WorkingHours { get; set; }

        public override void MapFromEntity(Site entity)
        {
            base.MapFromEntity(entity);
            ExpertsAmount = entity.Experts.Count;
            BeveragesAmount = entity.Beverages.Count;
            RoutesAmount = entity.Routes.Count;
            ActiveExpertsAmount = entity.Experts.Where(r => r.IsActive).Count();
            ActiveBeveragesAmount = entity.Beverages.Where(r => r.IsActive).Count();
            ActiveRoutesAmount = entity.Routes.Where(r => r.IsActive).Count();
            WorkingHours = DayOfWeekInterval.Parse(entity.WorkingHours);
            BeverageStepOrder = entity.BeverageStepOrder;
            ExpertStepOrder = entity.ExpertStepOrder;
            RouteStepOrder = entity.RouteStepOrder;
        }

        public override Site MapToEntity()
        {
            Site entity = base.MapToEntity();
            entity.WorkingHours = String.Join(DayOfWeekInterval.DayOfWeekIntervalsSeparator.ToString(), DayOfWeekInterval.Purify(WorkingHours).Select(r => r.ToString()));
            return entity;
        }
    }
}
