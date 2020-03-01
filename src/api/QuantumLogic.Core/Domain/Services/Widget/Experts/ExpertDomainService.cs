using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Services.Shared.Urls;
using QuantumLogic.Core.Domain.Validation.Widget;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Experts
{
    public class ExpertDomainService : EntityExtendedDomainService<Expert, int>, IExpertDomainService
    {
        public override int DefaultOrder => 1;

        #region Injected dependencies

        protected readonly ILeadRepository LeadRepository;
        protected readonly IImageUrlService ImageUrlService;

        #endregion

        #region Ctors

        public ExpertDomainService(IExpertRepository repository, IExpertPolicy policy, IExpertValidationService validationService, ILeadRepository leadRepository, IImageUrlService imageUrlService)
            : base(repository, policy, validationService)
        {
            LeadRepository = leadRepository;
            ImageUrlService = imageUrlService;
        }

        #endregion

        public override async Task<Expert> CreateAsync(Expert entity)
        {
            entity.PhotoUrl = await ImageUrlService.Transform(entity.PhotoUrl);
            return await base.CreateAsync(entity);
        }
        public override async Task<Expert> UpdateAsync(Expert entity)
        {
            entity.PhotoUrl = await ImageUrlService.Transform(entity.PhotoUrl);
            return await base.UpdateAsync(entity);
        }

        #region Helpers

        protected override Task CascadeDeleteActionAsync(Expert entity)
        {
            return Task.WhenAll(
                LeadRepository.NullifyExpertRelation(entitySet => entitySet.Where(r => r.ExpertId == entity.Id)),
                ImageUrlService.RemoveAsync(entity.PhotoUrl));
        }
        internal override IEnumerable<LoadEntityRelationAction<Expert>> GetLoadEntityRelationActions()
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
        protected override async Task SetOrderForEntityOnCreate(Expert entity)
        {
            entity.Order = (await ((IExpertRepository)Repository)
                    .GetMaxExistedOrder((qb) => qb.Where(r => r.SiteId == entity.SiteId))) + 1 ??
                DefaultOrder;
        }

        #endregion
    }
}
