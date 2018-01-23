using System.Collections;
using System.Collections.Generic;
using System.Linq;
using QuantumLogic.Core.Domain.Entities.MainModule;

namespace QuantumLogic.WebApi.DataModels.Dtos.Main.Users
{
    public class UserFullDto : UserDto
    {
        public ICollection<int> RoleIds { get; set; }

        public override void MapFromEntity(User entity)
        {
            base.MapFromEntity(entity);
            RoleIds = entity.UserRoles.Select(r => r.RoleId).ToList();
        }

        public override User MapToEntity()
        {
            User entity = base.MapToEntity();
            entity.UserRoles = RoleIds.Select(r => new UserRole() { RoleId = r, UserId = entity.Id }).ToList();
            return entity;
        }
    }
}