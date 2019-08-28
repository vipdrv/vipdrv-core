using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.WebApi.DataModels.Dtos;
using System.Threading.Tasks;
using QuantumLogic.Core.Domain.Services;
using QuantumLogic.Core.Domain.UnitOfWorks;

namespace QuantumLogic.WebApi.Controllers
{
    public abstract class EntityExtendedController<TEntity, TPrimaryKey, TEntityDto, TEntityFullDto> :
        EntityController<TEntity, TPrimaryKey, TEntityDto, TEntityFullDto>
        where TEntity : class, IEntity<TPrimaryKey>, IPassivable, IOrderable
        where TEntityDto : class, IEntityDto<TEntity, TPrimaryKey>, IPassivable, IOrderable, new()
        where TEntityFullDto : TEntityDto, new()
    {
        #region Ctors

        public EntityExtendedController(IQLUnitOfWorkManager uowManager, IEntityExtendedDomainService<TEntity, TPrimaryKey> domainService)
            : base(uowManager, domainService)
        { }

        #endregion

        protected virtual async Task ChangeActivityAsync(TPrimaryKey id, bool newValue)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((IEntityExtendedDomainService<TEntity, TPrimaryKey>)DomainService).ChangeActivityAsync(id, newValue);
                await uow.CompleteAsync();
            }
        }
        protected virtual async Task ChangeOrderAsync(TPrimaryKey id, int newValue)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((IEntityExtendedDomainService<TEntity, TPrimaryKey>)DomainService).ChangeOrderAsync(id, newValue);
                await uow.CompleteAsync();
            }
        }
        protected virtual async Task SwapOrdersAsync(TPrimaryKey key1, TPrimaryKey key2)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await ((IEntityExtendedDomainService<TEntity, TPrimaryKey>)DomainService).SwapOrdersAsync(key1, key2);
                await uow.CompleteAsync();
            }
        }
    }
}
