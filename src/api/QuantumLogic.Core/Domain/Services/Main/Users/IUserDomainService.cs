using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Services.Main.Users.Models;
using System;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Main.Users
{
    public interface IUserDomainService : IEntityDomainService<User, int>
    {
        Task<UserInfo> GetUserInfoAsync(string login, string password, Func<User, string, bool> loginComparer);
        Task<bool> IsUsernameValidAsync(string value);
        Task UpdatePasswordAsync(int userId, string oldPassword, string newPassword);
        Task UpdateAvatarAsync(int userId, string newAvatarUrl);
        Task UpdatePersonalInfoAsync(int userId, string newFirstName, string newSecondName, string newEmail, string newPhoneNumber);
    }
}
