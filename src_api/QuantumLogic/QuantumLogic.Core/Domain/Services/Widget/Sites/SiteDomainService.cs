using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Services.Shared.Urls;
using QuantumLogic.Core.Domain.Validation.Widget;
using QuantumLogic.Core.Utils.Scheduling.Week;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Sites
{
    public class SiteDomainService : EntityDomainService<Site, int>, ISiteDomainService
    {
        #region Injected dependencies

        protected readonly ILeadRepository LeadRepository;
        protected readonly IImageUrlService ImageUrlService;

        #endregion

        #region Ctors

        public SiteDomainService(ISiteRepository repository, ISitePolicy policy, ISiteValidationService validationService, ILeadRepository leadRepository, IImageUrlService imageUrlService)
            : base(repository, policy, validationService)
        {
            LeadRepository = leadRepository;
            ImageUrlService = imageUrlService;
        }

        #endregion

        public override async Task<Site> CreateAsync(Site entity)
        {
            entity.Steps = CreateSteps(entity);
            entity.ImageUrl = await ImageUrlService.Transform(entity.ImageUrl);
            return await base.CreateAsync(entity);
        }
        public override async Task<Site> UpdateAsync(Site entity)
        {
            entity.ImageUrl = await ImageUrlService.Transform(entity.ImageUrl);
            return await base.UpdateAsync(entity);
        }

        public async Task ChangeContactsAsync(int id, string newValue)
        {
            Site entity = await RetrieveAsync(id);
            ((ISitePolicy)Policy).CanUpdate(entity);
            entity.NotificationContacts = newValue;
            ((ISiteValidationService)ValidationService).ValidateChangeContacts(entity);
            await Repository.UpdateAsync(entity);
        }
        public async Task<IList<DayOfWeekInterval>> RetrieveWeekSchedule(int id)
        {
            return DayOfWeekInterval.Purify((await RetrieveAsync(id)).Experts.Where(r => r.IsActive).SelectMany(r => DayOfWeekInterval.Parse(r.WorkingHours)).ToList());
        }

        #region Helpers

        protected virtual List<Step> CreateSteps(Site entity)
        {
            return new List<Step>()
            {
                new Step()
                {
                    Descriptor = Step.DescriptorStepSchedule,
                    Name = Step.DescriptorStepSchedule,
                    Order = 1,
                    IsActive = true
                },
                new Step()
                {
                    Descriptor = Step.DescriptorStepExpert,
                    Name = Step.DescriptorStepExpert,
                    Order = 2,
                    IsActive = true
                },
                new Step()
                {
                    Descriptor = Step.DescriptorStepBeverage,
                    Name = Step.DescriptorStepBeverage,
                    Order = 3,
                    IsActive = true
                },
                new Step()
                {
                    Descriptor = Step.DescriptorStepRoute,
                    Name = Step.DescriptorStepRoute,
                    Order = 4,
                    IsActive = true
                },
                new Step()
                {
                    Descriptor = Step.DescriptorStepMusic,
                    Name = Step.DescriptorStepMusic,
                    Order = 5,
                    IsActive = false
                }
            };
        }
        protected override Task CascadeDeleteActionAsync(Site entity)
        {
            return Task.WhenAll(
                LeadRepository.DeleteRange(entitySet => entitySet.Where(r => r.SiteId == entity.Id)),
                ImageUrlService.RemoveAsync(entity.ImageUrl));
        }
        internal override IEnumerable<LoadEntityRelationAction<Site>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Site>>();
        }
        protected override Expression<Func<Site, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Site, object>>>()
            {
                //entity => entity.Beverages,
                //entity => entity.Experts,
                entity => entity.Leads,
                //entity => entity.Routes,
                entity => entity.User,
                entity => entity.Steps,
                //entity => entity.WidgetTheme
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
                entity => entity.Steps,
                entity => entity.WidgetTheme
            }
            .ToArray();
        }

        #endregion
    }
}
