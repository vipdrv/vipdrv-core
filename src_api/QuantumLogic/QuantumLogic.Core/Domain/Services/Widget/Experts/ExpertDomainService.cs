using QuantumLogic.Core.Domain.Context;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Experts
{
    public class ExpertDomainService : EntityExtendedDomainService<Expert, int>, IExpertDomainService
    {
        #region Ctors

        public ExpertDomainService(IDomainContext domainContext, IExpertRepository repository, IExpertPolicy policy, IExpertValidationService validationService)
            : base(domainContext, repository, policy, validationService)
        { }

        #endregion

        protected override Task CascadeDeleteAction(Expert entity)
        {
            return Task.CompletedTask;
        }
        protected override IEnumerable<LoadEntityRelationAction<Expert>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Expert>>();
        }
        protected override Expression<Func<Expert, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Expert, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
        protected override Expression<Func<Expert, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<Expert, object>>>()
            {
                entity => entity.Site
            }
            .ToArray();
        }
    }
}
