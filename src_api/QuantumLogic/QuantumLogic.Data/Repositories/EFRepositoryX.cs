using Microsoft.EntityFrameworkCore;
using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Repositories;
using QuantumLogic.Data.EFContext;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Data.Repositories
{
    public class EFRepositoryX<TEntity, TPrimaryKey> : EFRepository<TEntity, TPrimaryKey>, IQLRepositoryX<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IPassivable, IOrderable
    {
        #region Ctors

        public EFRepositoryX(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public EFRepositoryX(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion

        public async Task<int?> GetMaxExistedOrder(Func<IQueryable<TEntity>, IQueryable<TEntity>> queryBuilder, params Expression<Func<TEntity, object>>[] includes)
        {
            bool createdNew;
            DbSet<TEntity> set = DbContextManager.BuildOrCurrentContext(out createdNew).Set<TEntity>();
            IQueryable<TEntity> query = OnSystemFilters ?
                await ApplySystemFilters(queryBuilder(ApplyIncludes(set, includes))) :
                queryBuilder(ApplyIncludes(set, includes));
            int? maxExistedOrder;
            try
            {
                maxExistedOrder = query.Max(r => r.Order);
            }
            catch (InvalidOperationException)
            {
                // means query was empty
                maxExistedOrder = null;
            }
            IList<TEntity> entities = await query.ToListAsync();
            if (createdNew)
            {
                DbContextManager.DisposeContext();
            }
            return maxExistedOrder;
        }
    }
}
