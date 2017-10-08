using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Services.Main.Users.Models;
using System;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Main.Users
{
    public interface IUserDomainService : IEntityDomainService<User, int>
    {
        Task<UserInfo> GetUserInfo(string login, string password, Func<User, string, bool> loginComparer);
    }
}
