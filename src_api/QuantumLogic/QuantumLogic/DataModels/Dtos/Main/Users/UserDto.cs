using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.MainModule;

namespace QuantumLogic.WebApi.DataModels.Dtos.Main.Users
{
    public class UserDto : EntityDto<User, int>
    {
        public string Email { get; set; }
        public string Password { get; set; }
        public int MaxSitesCount { get; set; }
        public int CurrentSitesCount { get; set; }

        public override void MapFromEntity(User entity)
        {
            base.MapFromEntity(entity);
            Email = entity.Email;
            Password = QuantumLogicConstants.FakePassword;
            MaxSitesCount = entity.MaxSitesCount;
            CurrentSitesCount = entity.Sites.Count;
        }
        public override User MapToEntity()
        {
            User entity = base.MapToEntity();
            entity.Email = Email;
            entity.Password = Password;
            entity.MaxSitesCount = MaxSitesCount;
            return entity;
        }

        public override void NormalizeAsRequest()
        { }
        public override void NormalizeAsResponse()
        { }
    }
}
