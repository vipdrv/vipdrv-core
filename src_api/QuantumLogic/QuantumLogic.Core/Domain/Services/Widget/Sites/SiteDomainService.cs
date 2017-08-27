using QuantumLogic.Core.Domain.Context;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Sites
{
    public class SiteDomainService : EntityDomainService<Site, int>, ISiteDomainService
    {
        #region Ctors

        public SiteDomainService(IDomainContext domainContext, ISiteRepository repository, ISitePolicy policy, ISiteValidationService validationService)
            : base(domainContext, repository, policy, validationService)
        { }

        #endregion

        protected override Task CascadeDeleteAction(Site entity)
        {
            return Task.CompletedTask;
        }
        protected override IEnumerable<LoadEntityRelationAction<Site>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Site>>();
        }
        protected override Expression<Func<Site, object>>[] GetRetrieveAllEntityIncludes()
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
