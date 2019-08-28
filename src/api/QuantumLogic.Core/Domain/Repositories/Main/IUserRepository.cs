using QuantumLogic.Core.Domain.Entities.MainModule;
using System;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Repositories.Main
{
    public interface IUserRepository : IQLRepository<User, int>
    {
        /// <summary>
        /// This method is ad-hock to avoid nested includes in EF core (should be removed after solution to use nested includes as input parameter)
        /// </summary>
        /// <param name="filter">filter</param>
        /// <returns>task with first or default user as result</returns>
        Task<User> FirstOrDefaultWithDeepIncludesAsync(Expression<Func<User, bool>> filter);
    }
}
