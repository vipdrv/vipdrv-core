using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.WidgetEvents
{
    public class WidgetEventDomainService : EntityDomainService<WidgetEvent, int>, IWidgetEventDomainService
    {
        #region Ctors

        public WidgetEventDomainService(IWidgetEventRepository repository, IWidgetEventPolicy policy, IWidgetEventValidationService validationService)
            : base(repository, policy, validationService)
        { }

        #endregion
        
        public async Task ChangeIsResolvedAsync(int id, bool value)
        {
            WidgetEvent entity = await RetrieveAsync(id);
            entity.IsResolved = value;
            await Repository.UpdateAsync(entity);
        }

        protected override Task CascadeDeleteActionAsync(WidgetEvent entity)
        {
            return Task.CompletedTask;
        }
        internal override IEnumerable<LoadEntityRelationAction<WidgetEvent>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<WidgetEvent>>();
        }
        protected override Expression<Func<WidgetEvent, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<WidgetEvent, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
        protected override Expression<Func<WidgetEvent, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<WidgetEvent, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
    }
}
