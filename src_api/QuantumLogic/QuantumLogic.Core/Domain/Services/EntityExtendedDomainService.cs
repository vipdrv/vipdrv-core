using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Context;
using QuantumLogic.Core.Domain.Policy;
using QuantumLogic.Core.Domain.Repositories;
using QuantumLogic.Core.Domain.Validation;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services
{
    public abstract class EntityExtendedDomainService<TEntity, TPrimaryKey> : EntityDomainService<TEntity, TPrimaryKey>, IEntityExtendedDomainService<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IUpdatableFrom<TEntity>, IPassivable, IOrderable
    {
        #region Ctors

        public EntityExtendedDomainService(IDomainContext domainContext, IQLRepository<TEntity, TPrimaryKey> repository, IEntityExtendedPolicy<TEntity, TPrimaryKey> policy, IEntityExtendedValidationService<TEntity, TPrimaryKey> validationService) 
            : base(domainContext, repository, policy, validationService)
        { }

        #endregion

        public virtual async Task ChangeActivityAsync(TPrimaryKey id, bool newValue)
        {
            TEntity entity = await RetrieveAsync(id);
            ((IEntityExtendedPolicy<TEntity, TPrimaryKey>)Policy).PolicyChangeActivity(entity);
            entity.IsActive = newValue;
            ((IEntityExtendedValidationService<TEntity, TPrimaryKey>)ValidationService).ValidateChangeActivity(entity);
            await Repository.UpdateAsync(entity);
        }
        public virtual async Task ChangeOrderAsync(TPrimaryKey id, int newValue)
        {
            TEntity entity = await RetrieveAsync(id);
            ((IEntityExtendedPolicy<TEntity, TPrimaryKey>)Policy).PolicyChangeOrder(entity);
            entity.Order = newValue;
            ((IEntityExtendedValidationService<TEntity, TPrimaryKey>)ValidationService).ValidateChangeOrder(entity);
            await Repository.UpdateAsync(entity);
        }
    }
}
