using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Beverages
{
    public class BeverageDomainService : EntityExtendedDomainService<Beverage, int>, IBeverageDomainService
    {
        public override int DefaultOrder => 1;

        #region Injected dependencies

        protected readonly ILeadRepository LeadRepository;

        #endregion

        #region Ctors

        public BeverageDomainService(IBeverageRepository repository, IBeveragePolicy policy, IBeverageValidationService validationService, ILeadRepository leadRepository)
            : base(repository, policy, validationService)
        {
            LeadRepository = leadRepository;
        }

        #endregion

        protected override Task CascadeDeleteActionAsync(Beverage entity)
        {
            return LeadRepository.NullifyBeverageRelation(entitySet => entitySet.Where(r => r.BeverageId == entity.Id));
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
        protected override async Task SetOrderForEntityOnCreate(Beverage entity)
        {
            entity.Order = (await ((IBeverageRepository)Repository)
                    .GetMaxExistedOrder((qb) => qb.Where(r => r.SiteId == entity.SiteId))) + 1 ??
                DefaultOrder;
        }
    }
}
