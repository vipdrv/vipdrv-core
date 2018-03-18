using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Steps
{
    public class StepDomainService : EntityExtendedDomainService<Step, int>, IStepDomainService
    {
        public override int DefaultOrder => 1;

        #region Ctors

        public StepDomainService(IStepRepository repository, IStepPolicy policy, IStepValidationService validationService)
            : base(repository, policy, validationService)
        { }

        #endregion

        protected override Task CascadeDeleteActionAsync(Step entity)
        {
            return Task.CompletedTask;
        }
        internal override IEnumerable<LoadEntityRelationAction<Step>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Step>>();
        }
        protected override Expression<Func<Step, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Step, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
        protected override Expression<Func<Step, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<Step, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
        protected override async Task SetOrderForEntityOnCreate(Step entity)
        {
            entity.Order = (await ((IExpertRepository)Repository)
                    .GetMaxExistedOrder((qb) => qb.Where(r => r.SiteId == entity.SiteId))) + 1 ??
                DefaultOrder;
        }
    }
}
