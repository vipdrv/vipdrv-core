using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Routes
{
    public class RouteDomainService : EntityExtendedDomainService<Route, int>, IRouteDomainService
    {
        public override int DefaultOrder => 1;

        #region Ctors

        public RouteDomainService(IRouteRepository repository, IRoutePolicy policy, IRouteValidationService validationService)
            : base(repository, policy, validationService)
        { }

        #endregion

        protected override Task CascadeDeleteActionAsync(Route entity)
        {
            return Task.CompletedTask;
        }
        internal override IEnumerable<LoadEntityRelationAction<Route>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Route>>();
        }
        protected override Expression<Func<Route, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Route, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
        protected override Expression<Func<Route, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<Route, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
        protected override async Task SetOrderForEntityOnCreate(Route entity)
        {
            entity.Order = (await ((IRouteRepository)Repository)
                    .GetMaxExistedOrder((qb) => qb.Where(r => r.SiteId == entity.SiteId))) + 1 ?? 
                DefaultOrder;
        }
    }
}
