using QuantumLogic.Core.Domain.Context;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Routes
{
    public class RouteDomainService : EntityExtendedDomainService<Route, int>, IRouteDomainService
    {
        #region Ctors

        public RouteDomainService(IDomainContext domainContext, IRouteRepository repository, IRoutePolicy policy, IRouteValidationService validationService)
            : base(domainContext, repository, policy, validationService)
        { }

        #endregion

        protected override Task CascadeDeleteActionAsync(Route entity)
        {
            return Task.CompletedTask;
        }
        protected override IEnumerable<LoadEntityRelationAction<Route>> GetLoadEntityRelationActions()
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
    }
}
