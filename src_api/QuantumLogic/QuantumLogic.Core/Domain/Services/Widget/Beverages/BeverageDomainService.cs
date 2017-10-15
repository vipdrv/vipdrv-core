using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Beverages
{
    public class BeverageDomainService : EntityExtendedDomainService<Beverage, int>, IBeverageDomainService
    {
        #region Ctors

        public BeverageDomainService(IBeverageRepository repository, IBeveragePolicy policy, IBeverageValidationService validationService)
            : base(repository, policy, validationService)
        { }

        #endregion

        protected override Task CascadeDeleteActionAsync(Beverage entity)
        {
            return Task.CompletedTask;
        }
        internal override IEnumerable<LoadEntityRelationAction<Beverage>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Beverage>>();
        }
        protected override Expression<Func<Beverage, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Beverage, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
        protected override Expression<Func<Beverage, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<Beverage, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
    }
}
