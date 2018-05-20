using OfficeOpenXml;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using QuantumLogic.Core.Extensions.DateTimeEx;
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Leads
{
    public class LeadDomainService : EntityDomainService<Lead, int>, ILeadDomainService
    {
        #region Ctors

        public LeadDomainService(ILeadRepository repository, ILeadPolicy policy, ILeadValidationService validationService)
            : base(repository, policy, validationService)
        { }

        #endregion

        public override Task<Lead> CreateAsync(Lead entity)
        {
            entity.RecievedUtc = DateTime.UtcNow;
            return base.CreateAsync(entity);
        }
        public async Task ChangeIsNewAsync(int id, bool newValue)
        {
            Lead entity = await RetrieveAsync(id);
            ((ILeadPolicy)Policy).PolicyUpdate(entity);
            entity.IsNew = newValue;
            ((ILeadValidationService)ValidationService).ValidateChangeIsNew(entity);
            await Repository.UpdateAsync(entity);
        }
        public async Task ChangeIsReachedByManagerAsync(int id, bool newValue)
        {
            Lead entity = await RetrieveAsync(id);
            ((ILeadPolicy)Policy).PolicyUpdate(entity);
            entity.IsReachedByManager = newValue;
            ((ILeadValidationService)ValidationService).ValidateChangeIsReachedByManager(entity);
            await Repository.UpdateAsync(entity);
        }

        protected override Task CascadeDeleteActionAsync(Lead entity)
        {
            return Task.CompletedTask;
        }
        protected override Expression<Func<Lead, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Lead, object>>>()
            {
                entity => entity.Beverage,
                entity => entity.Expert,
                entity => entity.Route,
                entity => entity.Site
            }
            .ToArray();
        }
        protected override Expression<Func<Lead, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<Lead, object>>>()
            {
                entity => entity.Beverage,
                entity => entity.Expert,
                entity => entity.Route,
                entity => entity.Site
            }
            .ToArray();
        }
        internal override IEnumerable<LoadEntityRelationAction<Lead>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Lead>>();
        }
    }
}
