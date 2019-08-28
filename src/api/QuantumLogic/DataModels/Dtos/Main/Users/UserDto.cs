using QuantumLogic.Core.Constants;
using QuantumLogic.Core.Domain.Entities.MainModule;

namespace QuantumLogic.WebApi.DataModels.Dtos.Main.Users
{
    public class UserDto : EntityDto<User, int>
    {
        public string Username { get; set; }
        public string Email { get; set; }
        public string PhoneNumber { get; set; }
        public string Password { get; set; }

        public string FirstName { get; set; }
        public string SecondName { get; set; }
        public string AvatarUrl { get; set; }

        public int MaxSitesCount { get; set; }
        public int CurrentSitesCount { get; set; }

        public override void MapFromEntity(User entity)
        {
            base.MapFromEntity(entity);
            Username = entity.Username;
            Email = entity.Email;
            PhoneNumber = entity.PhoneNumber;
            Password = QuantumLogicConstants.FakePassword;
            FirstName = entity.FirstName;
            SecondName = entity.SecondName;
            AvatarUrl = entity.AvatarUrl;
            MaxSitesCount = entity.MaxSitesCount;
            CurrentSitesCount = entity.Sites.Count;
        }
        public override User MapToEntity()
        {
            User entity = base.MapToEntity();
            entity.Username = Username;
            entity.Email = Email;
            entity.PhoneNumber = PhoneNumber;
            entity.PasswordHash = Password;
            entity.FirstName = FirstName;
            entity.SecondName = SecondName;
            entity.AvatarUrl = AvatarUrl;
            entity.MaxSitesCount = MaxSitesCount;
            return entity;
        }

        public override void NormalizeAsRequest()
        { }
        public override void NormalizeAsResponse()
        { }
    }
}
