using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using QuantumLogic.Core.Utils.Scheduling.Week;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Sites
{
    public class SiteDomainService : EntityDomainService<Site, int>, ISiteDomainService
    {
        #region Ctors

        public SiteDomainService(ISiteRepository repository, ISitePolicy policy, ISiteValidationService validationService)
            : base(repository, policy, validationService)
        { }

        #endregion

        public async Task ChangeContactsAsync(int id, string newValue)
        {
            Site entity = await RetrieveAsync(id);
            ((ISitePolicy)Policy).CanUpdate(entity);
            entity.NotificationContacts = newValue;
            ((ISiteValidationService)ValidationService).ValidateChangeContacts(entity);
            await Repository.UpdateAsync(entity);
        }
        public async Task<IList<DayOfWeekInterval>> RetrieveWeekSchedule(int id)
        {
            return DayOfWeekInterval.Purify((await RetrieveAsync(id)).Experts.Where(r => r.IsActive).SelectMany(r => DayOfWeekInterval.Parse(r.WorkingHours)).ToList());
        }

        protected override Task CascadeDeleteActionAsync(Site entity)
        {
            return Task.CompletedTask;
        }
        internal override IEnumerable<LoadEntityRelationAction<Site>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Site>>();
        }
        protected override Expression<Func<Site, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Site, object>>>()
            {
                //entity => entity.Beverages,
                //entity => entity.Experts,
                entity => entity.Leads,
                //entity => entity.Routes,
                entity => entity.User,
                //entity => entity.WidgetTheme
            }
            .ToArray();
        }
        protected override Expression<Func<Site, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<Site, object>>>()
            {
                entity => entity.Beverages,
                entity => entity.Experts,
                entity => entity.Leads,
                entity => entity.Routes,
                entity => entity.User,
                entity => entity.WidgetTheme
            }
            .ToArray();
        }
    }
}
