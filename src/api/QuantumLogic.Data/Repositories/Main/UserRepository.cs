using Microsoft.EntityFrameworkCore;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Repositories.Main;
using QuantumLogic.Data.EFContext;
using System;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Data.Repositories.Main
{
    public class UserRepository : EFRepository<User, int>, IUserRepository
    {
        #region Ctors

        public UserRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public UserRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion

        public virtual async Task<User> FirstOrDefaultWithDeepIncludesAsync(Expression<Func<User, bool>> filter)
        {
            bool createdNew = false;
            try
            {
                DbSet<User> set = DbContextManager.BuildOrCurrentContext(out createdNew).Set<User>();
                var query = set
                    .Include(r => r.UserClaims)
                        .ThenInclude(r => r.Claim)
                    .Include(r => r.UserRoles)
                        .ThenInclude(r => r.Role)
                            .ThenInclude(r => r.RoleClaims)
                                .ThenInclude(r => r.Claim)
                    .Include(r => r.ExternalLogins);
                IQueryable<User> temp = OnSystemFilters ?
                    await ApplySystemFilters(Queryable.Where(query, filter)) :
                    Queryable.Where(query, filter);
                User entity = await temp.FirstOrDefaultAsync();
                return entity;
            }
            finally
            {
                if (createdNew)
                {
                    DbContextManager.DisposeContext();
                }
            }
        }
    }
}
