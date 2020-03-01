using Microsoft.AspNetCore.Mvc;
using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Services;
using QuantumLogic.Core.Domain.UnitOfWorks;
using QuantumLogic.WebApi.DataModels;
using QuantumLogic.WebApi.DataModels.Dtos;
using QuantumLogic.WebApi.DataModels.Responses;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.WebApi.Controllers
{
    /// <summary>
    /// Is used as base controller for entities
    /// </summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    /// <typeparam name="TPrimaryKey">type of entity primary key</typeparam>
    /// <typeparam name="TEntityDto">type of entity dto</typeparam>
    /// <typeparam name="TEntityFullDto">type of entity dto with allowed relations (full info about entity)</typeparam>
    public abstract class EntityController<TEntity, TPrimaryKey, TEntityDto, TEntityFullDto> : Controller
        where TEntity : class, IEntity<TPrimaryKey>
        where TEntityDto : class, IEntityDto<TEntity, TPrimaryKey>, new()
        where TEntityFullDto : TEntityDto, new()
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

        #region Inner CRUD methods

        /// <summary>
        /// Is used as inner get method for entity (returns full version of entity)
        /// </summary>
        /// <param name="id">entity id</param>
        /// <returns>entity as result of task</returns>
        protected virtual async Task<TEntityFullDto> InnerGetAsync(TPrimaryKey id)
        {
            TEntity entity;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                entity = await DomainService.RetrieveAsync(id);
            }
            TEntityFullDto response = new TEntityFullDto();
            response.MapFromEntity(entity);
            response.NormalizeAsResponse();
            return response;
        }
        /// <summary>
        /// Is used as inner create method for entity (creates entity and returns it back via <see cref="InnerGetAsync(TPrimaryKey)"/>) 
        /// </summary>
        /// <param name="request">request</param>
        /// <returns>created entity as result of task</returns>
        /// <exception cref="ArgumentNullException">Thrown when request is null</exception>
        protected virtual async Task<TEntityFullDto> InnerCreateAsync(TEntityFullDto request)
        {
            if (request == null)
            {
                throw new ArgumentNullException(nameof(request));
            }
            request.NormalizeAsRequest();
            TPrimaryKey entityId;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                TEntity stubEntity = await DomainService.CreateAsync(request.MapToEntity());
                await uow.CompleteAsync();
                entityId = stubEntity.Id;
            }
            return await InnerGetAsync(entityId);
        }
        /// <summary>
        /// Is used as inner update method for entity (updates entity and returns it back via <see cref="InnerGetAsync(TPrimaryKey)"/>) 
        /// </summary>
        /// <param name="request">request</param>
        /// <returns>updated entity as result of task</returns>
        /// <exception cref="ArgumentNullException">Thrown when request is null</exception>
        protected virtual async Task<TEntityFullDto> InnerUpdateAsync(TEntityFullDto request)
        {
            if (request == null)
            {
                throw new ArgumentNullException("Request");
            }
            request.NormalizeAsRequest();
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await DomainService.UpdateAsync(request.MapToEntity());
                await uow.CompleteAsync();
            }
            return await InnerGetAsync(request.Id);
        }
        /// <summary>
        /// Is used as inner delete method for entity
        /// </summary>
        /// <param name="id">entity id</param>
        /// <returns>deletion task</returns>
        protected virtual async Task InnerDeleteAsync(TPrimaryKey id)
        {
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                await DomainService.DeleteAsync(id);
                await uow.CompleteAsync();
            }
        }

        #endregion

        #region Inner methods to operate with many entities

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
            int totalCount;
            IList<TEntity> entities;
            int skip = (int)(page * pageSize);
            int take = (int)pageSize;
            using (var uow = UowManager.CurrentOrCreateNew(true))
            {
                entities = await DomainService.RetrieveAllAsync(filter, sorting, skip, take);
                totalCount = take > 0 || skip > 0 && (entities.Count == take || skip != 0) ? await DomainService.GetTotalCountAsync(filter) : entities.Count;
            }
            return new GetAllResponse<TEntityDto>(MapEntitiesToDtos<TEntity, TEntityDto>(entities), totalCount);
        }

        #endregion

        #region Helpers

        protected virtual List<TTo> MapEntitiesToDtos<TFrom, TTo>(IList<TFrom> entities)
            where TTo : class, IMapable<TFrom>, IShouldNormalize, new()
        {
            List<TTo> entityDtos = new List<TTo>(entities.Count);
            foreach (TFrom entity in entities)
            {
                TTo entityDto = new TTo();
                entityDto.MapFromEntity(entity);
                entityDto.NormalizeAsResponse();
                entityDtos.Add(entityDto);
            }
            return entityDtos;
        }

        #endregion
    }
}
