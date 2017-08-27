using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Services;
using QuantumLogic.Core.Domain.Services.Models;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels.Dtos;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
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

        protected virtual async Task<TEntityDto> InnerGetAsync(TPrimaryKey id)
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
        protected virtual async Task<TEntityDto> InnerCreateAsync(TEntityDto request)
        {
            TEntityDto response = new TEntityDto();
            TPrimaryKey entityId;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                TEntity stubEntity = await DomainService.CreateAsync(request.MapToEntity());
                await uow.CompleteAsync();
                entityId = stubEntity.Id;
            }
            response.MapFromEntity(await DomainService.RetrieveAsync(entityId));
            response.NormalizeAsResponse();
            return response;
        }
        protected virtual async Task<TEntityDto> InnerUpdateAsync(TEntityDto request)
        {
            TEntityDto response = new TEntityDto();
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await DomainService.UpdateAsync(request.MapToEntity());
                await uow.CompleteAsync();
            }
            response.MapFromEntity(await DomainService.RetrieveAsync(request.Id));
            response.NormalizeAsResponse();
            return response;
        }
        protected virtual async Task InnerDeleteAsync(TPrimaryKey id)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await DomainService.DeleteAsync(id);
                await uow.CompleteAsync();
            }
        }

        #endregion

        /// <summary>
        /// Is used to retrieve all entities via filters and sorting as paged result
        /// </summary>
        /// <param name="filter">filter</param>
        /// <param name="sorting">sorting as string like "prop1 asc, prop2 desc"</param>
        /// <param name="page">page number (starts from 0)</param>
        /// <param name="pageSize">page size (count of elements on the page)</param>
        /// <returns>object with retrieved items and total count</returns>
        protected virtual async Task<GetAllResponse<TEntityDto>> InnerGetAllAsync(Expression<Func<TEntity, bool>> filter, string sorting, uint page, uint pageSize)
        {
            RetrieveAllResultModel<TEntity> stub;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                stub = await DomainService.RetrieveAllAsync(filter, sorting, (int)(page * pageSize), (int)pageSize);
            }
            List<TEntityDto> items = new List<TEntityDto>(stub.Entities.Count);
            foreach (var entity in stub.Entities)
            {
                TEntityDto entityDto = new TEntityDto();
                entityDto.MapFromEntity(entity);
                entityDto.NormalizeAsResponse();
                items.Add(entityDto);
            }
            return new GetAllResponse<TEntityDto>(items, stub.TotalCount);
        }
    }
}
