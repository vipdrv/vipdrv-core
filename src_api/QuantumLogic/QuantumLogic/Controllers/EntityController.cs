using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Services;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers
{
    public abstract class EntityController<TEntity, TPrimaryKey, TEntityDto> : Controller
        where TEntity : class, IEntity<TPrimaryKey>
        where TEntityDto : class, IEntityDto<TEntity, TPrimaryKey>, new()
    {
        #region Injected Dependencies

        public IQLUnitOfWorkManager UowManager { get; private set; }
        public IEntityDomainService<TEntity, TPrimaryKey> DomainService { get; private set; }

        #endregion

        #region Ctors

        public EntityController(IQLUnitOfWorkManager uowManager, IEntityDomainService<TEntity, TPrimaryKey> domainService)
            : base()
        {
            UowManager = uowManager;
            DomainService = domainService;
        }

        #endregion

        #region Inner controller CRUD methods

        protected virtual async Task<TEntityDto> InnerGet(TPrimaryKey id)
        {
            TEntity entity;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                entity = await DomainService.RetrieveAsync(id);
            }
            TEntityDto response = new TEntityDto();
            response.MapFromEntity(entity);
            response.NormalizeAsResponse();
            return response;
        }
        protected virtual async Task<TEntityDto> InnerCreate(TEntityDto request)
        {
            TEntityDto response = new TEntityDto();
            TPrimaryKey entityId;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                TEntity stubEntity = await DomainService.CreateAsync(request.MapToEntity());
                await uow.CompleteAsync();
                entityId = stubEntity.Id;
            }
            using (var uow = UowManager.CurrentOrCreateNew(false))
            {
                response.MapFromEntity(await DomainService.RetrieveAsync(entityId));
                response.NormalizeAsResponse();
            }
            return response;
        }
        protected virtual async Task<TEntityDto> InnerUpdate(TEntityDto request)
        {
            TEntityDto response = new TEntityDto();
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await DomainService.UpdateAsync(request.MapToEntity());
                await uow.CompleteAsync();
            }
            using (var uow = UowManager.CurrentOrCreateNew(false))
            {
                response.MapFromEntity(await DomainService.RetrieveAsync(request.Id));
                response.NormalizeAsResponse();
            }
            return response;
        }
        protected virtual async Task InnerDelete(TPrimaryKey id)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await DomainService.DeleteAsync(id);
                await uow.CompleteAsync();
            }
        }

        #endregion
    }
}
