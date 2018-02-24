using System.Collections.Generic;
using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Utils.Scheduling.Week;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Experts
{
    public class ExpertDto : EntityDto<Expert, int>, IPassivable, IOrderable
    {
        public int SiteId { get; set; }
        public string PhotoUrl { get; set; }
        public string Name { get; set; }
        public string Title { get; set; }
        public string Description { get; set; }
        public string Email { get; set; }
        public string PhoneNumber { get; set; }
        public string FacebookUrl { get; set; }
        public string LinkedinUrl { get; set; }
        public IList<DayOfWeekInterval> WorkingHours { get; set; }
        public int Order { get; set; }
        public bool IsActive { get; set; }

        public bool IsPartOfTeamNewCars { get; set; }
        public bool IsPartOfTeamUsedCars { get; set; }
        public bool IsPartOfTeamCPO { get; set; }

        #region Mapping

        public override void MapFromEntity(Expert entity)
        {
            base.MapFromEntity(entity);
            SiteId = entity.SiteId;
            PhotoUrl = entity.PhotoUrl;
            Name = entity.Name;
            Title = entity.Title;
            Description = entity.Description;
            Email = entity.Email;
            PhoneNumber = entity.PhoneNumber;
            FacebookUrl = entity.FacebookUrl;
            LinkedinUrl = entity.LinkedinUrl;
            WorkingHours = DayOfWeekInterval.Parse(entity.WorkingHours);
            Order = entity.Order;
            IsActive = entity.IsActive;
            IsPartOfTeamNewCars = entity.IsPartOfTeamNewCars;
            IsPartOfTeamUsedCars = entity.IsPartOfTeamUsedCars;
            IsPartOfTeamCPO = entity.IsPartOfTeamCPO;
        }
        public override Expert MapToEntity()
        {
            Expert entity = base.MapToEntity();
            entity.SiteId = SiteId;
            entity.PhotoUrl = PhotoUrl;
            entity.Name = Name;
            entity.Title = Title;
            entity.Description = Description;
            entity.Email = Email;
            entity.PhoneNumber = PhoneNumber;
            entity.FacebookUrl = FacebookUrl;
            entity.LinkedinUrl = LinkedinUrl;
            entity.Order = Order;
            entity.IsActive = IsActive;
            entity.IsPartOfTeamNewCars = IsPartOfTeamNewCars;
            entity.IsPartOfTeamUsedCars = IsPartOfTeamUsedCars;
            entity.IsPartOfTeamCPO = IsPartOfTeamCPO;
            return entity;
        }

        #endregion

        #region Normalization

        public override void NormalizeAsRequest()
        { }
        public override void NormalizeAsResponse()
        { }

        #endregion
    }
}
