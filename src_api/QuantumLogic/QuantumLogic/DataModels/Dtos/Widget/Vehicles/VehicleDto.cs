using QuantumLogic.Core.Domain.Entities.WidgetModule;
using System;

namespace QuantumLogic.WebApi.DataModels.Dtos.Widget.Vehicles
{
    public class VehicleDto : EntityDto<Vehicle, int>
    {
        public int SiteId { get; set; }
        public string Title { get; set; }
        public string ImageUrl { get; set; }
        public int Year { get; set; }
        public string Make { get; set; }
        public string Model { get; set; }
        public string Condition { get; set; }
        public string VIN { get; set; }
        public string Stock { get; set; }

        #region Mapping

        public override void MapFromEntity(Vehicle entity)
        {
            base.MapFromEntity(entity);
            SiteId = entity.SiteId;
            Title = entity.Title;
            ImageUrl = entity.ImageUrl;
            Year = entity.Year;
            Make = entity.Make;
            Model = entity.Model;
            Condition = entity.Condition.ToString();
            VIN = entity.VIN;
            Stock = entity.Stock;
        }
        public override Vehicle MapToEntity()
        {
            Vehicle entity = base.MapToEntity();
            entity.SiteId = SiteId;
            entity.Title = Title;
            entity.ImageUrl = ImageUrl;
            entity.Year = Year;
            entity.Make = Make;
            entity.Model = Model;
            entity.Condition = (VehicleConditions)Enum.Parse(typeof(VehicleConditions), Condition, true);
            entity.VIN = VIN;
            entity.Stock = Stock;
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
