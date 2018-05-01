using Microsoft.EntityFrameworkCore;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Data.EFContext;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace QuantumLogic.Data.Repositories.Widget
{
    public class LeadRepository : EFRepository<Lead, int>, ILeadRepository
    {
        #region Ctors

        public LeadRepository(DbContextManager dbContextManager)
            : base(dbContextManager)
        { }

        public LeadRepository(DbContextManager dbContextManager, bool onSystemFilters)
            : base(dbContextManager, onSystemFilters)
        { }

        #endregion

        public async Task NullifyBeverageRelation(Func<IQueryable<Lead>, IQueryable<Lead>> queryBuilder)
        {
            bool createdNew = false;
            try
            {
                DbContext context = DbContextManager.BuildOrCurrentContext(out createdNew);
                List<Lead> leads = EntityFrameworkQueryableExtensions.AsNoTracking(queryBuilder(context.Set<Lead>())).ToList();
                foreach (var lead in leads)
                {
                    Lead stubLead = new Lead() { Id = lead.Id, BeverageId = null };
                    context.Set<Lead>().Attach(stubLead);
                    context.Entry(stubLead).Property(x => x.BeverageId).IsModified = true;
                }
            }
            finally
            {
                if (createdNew)
                {
                    await DbContextManager.CurrentContext.SaveChangesAsync();
                    DbContextManager.DisposeContext();
                }
            }
        }
        public async Task NullifyExpertRelation(Func<IQueryable<Lead>, IQueryable<Lead>> queryBuilder)
        {
            using (DbContext context = DbContextManager.BuildNewContext())
            {
                List<Lead> leads = EntityFrameworkQueryableExtensions.AsNoTracking(queryBuilder(context.Set<Lead>())).ToList();
                foreach (var lead in leads)
                {
                    Lead stubLead = new Lead() { Id = lead.Id, ExpertId = null };
                    context.Set<Lead>().Attach(stubLead);
                    context.Entry(stubLead).Property(x => x.ExpertId).IsModified = true;
                }
                await context.SaveChangesAsync();
            }
        }
        public async Task NullifyRouteRelation(Func<IQueryable<Lead>, IQueryable<Lead>> queryBuilder)
        {
            bool createdNew = false;
            try
            {
                DbContext context = DbContextManager.BuildOrCurrentContext(out createdNew);
                List<Lead> leads = EntityFrameworkQueryableExtensions.AsNoTracking(queryBuilder(context.Set<Lead>())).ToList();
                foreach (var lead in leads)
                {
                    Lead stubLead = new Lead() { Id = lead.Id, RouteId = null };
                    context.Set<Lead>().Attach(stubLead);
                    context.Entry(stubLead).Property(x => x.RouteId).IsModified = true;
                }
            }
            finally
            {
                if (createdNew)
                {
                    await DbContextManager.CurrentContext.SaveChangesAsync();
                    DbContextManager.DisposeContext();
                }
            }
        }
    }
}
