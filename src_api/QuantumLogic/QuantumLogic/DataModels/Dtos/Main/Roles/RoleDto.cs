using QuantumLogic.Core.Domain.Entities.MainModule;

namespace QuantumLogic.WebApi.DataModels.Dtos.Main.Roles
{
    public class RoleDto : EntityDto<Role, int>
    {
        public string Name { get; set; }
        public bool CanBeUsedForInvitation { get; set; }

        public override void MapFromEntity(Role entity)
        {
            base.MapFromEntity(entity);
            Name = entity.Name;
            CanBeUsedForInvitation = entity.CanBeUsedForInvitation;
        }
        public override Role MapToEntity()
        {
            Role entity = base.MapToEntity();
            entity.Name = Name;
            entity.CanBeUsedForInvitation = CanBeUsedForInvitation;
            return entity;
        }

        public override void NormalizeAsRequest()
        { }
        public override void NormalizeAsResponse()
        { }
    }
}
