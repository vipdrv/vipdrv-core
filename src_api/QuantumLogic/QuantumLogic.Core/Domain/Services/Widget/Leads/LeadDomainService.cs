using QuantumLogic.Core.Domain.Context;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Leads
{
    public class LeadDomainService : EntityDomainService<Lead, int>, ILeadDomainService
    {
        #region Ctors

        public LeadDomainService(IDomainContext domainContext, ILeadRepository repository, ILeadPolicy policy, ILeadValidationService validationService)
            : base(domainContext, repository, policy, validationService)
        { }

        #endregion

        protected override Task CascadeDeleteAction(Lead entity)
        {
            return Task.CompletedTask;
        }
        protected override IEnumerable<LoadEntityRelationAction<Lead>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Lead>>();
        }
        protected override Expression<Func<Lead, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Lead, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
        protected override Expression<Func<Lead, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<Lead, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
    }
}
