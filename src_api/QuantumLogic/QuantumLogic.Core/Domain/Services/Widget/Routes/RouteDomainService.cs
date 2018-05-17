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

        #region Injected dependencies

        protected readonly ILeadRepository LeadRepository;

        #endregion

        #region Ctors

        public RouteDomainService(IRouteRepository repository, IRoutePolicy policy, IRouteValidationService validationService, ILeadRepository leadRepository)
            : base(repository, policy, validationService)
        {
            LeadRepository = leadRepository;
        }

        #endregion

        protected override Task CascadeDeleteActionAsync(Route entity)
        {
            return LeadRepository.NullifyExpertRelation(entitySet => entitySet.Where(r => r.RouteId == entity.Id));
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
