using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Policy;
using QuantumLogic.Core.Domain.Repositories;
using QuantumLogic.Core.Domain.Validation;
using QuantumLogic.Core.Enums;
using QuantumLogic.Core.Exceptions.Validation;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Dynamic.Core;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services
{
    public abstract class EntityDomainService<TEntity, TPrimaryKey> : IEntityDomainService<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IUpdatableFrom<TEntity>
    {
        #region Settings

        protected Expression<Func<TEntity, object>>[] RetrieveAllEntityIncludes { get; private set; }
        protected Expression<Func<TEntity, object>>[] RetrieveEntityIncludes { get; private set; }
        internal IEnumerable<LoadEntityRelationAction<TEntity>> LoadEntityRelationActions { get; private set; }

        #endregion

        #region Injected dependencies

        protected IQLRepository<TEntity, TPrimaryKey> Repository { get; private set; }
        protected IEntityPolicy<TEntity, TPrimaryKey> Policy { get; private set; }
        protected IEntityValidationService<TEntity, TPrimaryKey> ValidationService { get; private set; }

        #endregion

        #region Ctors

        public EntityDomainService(
            IQLRepository<TEntity, TPrimaryKey> repository,
            IEntityPolicy<TEntity, TPrimaryKey> policy,
            IEntityValidationService<TEntity, TPrimaryKey> validationService)
            : base()
        {
            Repository = repository;
            Policy = policy;
            ValidationService = validationService;
            RetrieveAllEntityIncludes = GetRetrieveAllEntityIncludes();
            RetrieveEntityIncludes = GetRetrieveEntityIncludes();
            LoadEntityRelationActions = GetLoadEntityRelationActions();
        }

        #endregion

        public virtual Task<int> GetTotalCountAsync(Expression<Func<TEntity, bool>> filter = null)
        {
            return Repository.GetTotalCountAsync((entitySet) => ApplyPolicyAndFilterToQuery(entitySet, filter));
        }
        public virtual async Task<IList<TEntity>> RetrieveAllAsync(Expression<Func<TEntity, bool>> filter = null, string sorting = null, int skip = 0, int take = 0)
        {
            return await Repository.GetAllAsync(
                (entitySet) => 
                {
                    entitySet = ApplySortingToQuery(ApplyPolicyAndFilterToQuery(entitySet, filter), sorting);
                    if (take > 0 || skip > 0)
                    {
                        entitySet = entitySet
                            .Skip(skip)
                            .Take(take);
                    }
                    return entitySet;
                }, 
                RetrieveAllEntityIncludes);
        }
        public virtual async Task<TEntity> RetrieveAsync(TPrimaryKey id)
        {
            TEntity entity;
            entity = await Repository.GetAsync(id, RetrieveEntityIncludes);
            Policy.PolicyRetrieve(entity);
            return entity;
        }
        public virtual async Task<TEntity> CreateAsync(TEntity entity)
        {
            // loading relations for entity
            LoadEntityRelations(entity, DomainMethodNames.Create);
            // using policy
            Policy.PolicyCreate(entity);
            // use validation
            ValidationService.ValidateCreate(entity);
            // insert to database
            await Repository.CreateAsync(entity);
            // retrieving entity from database
            return entity;
        }
        public virtual async Task<TEntity> UpdateAsync(TEntity entity)
        {
            // retrieving old entity
            TEntity oldEntity = await RetrieveAsync(entity.Id);
            // loading relations for entity
            LoadEntityRelations(entity, DomainMethodNames.Update);
            // using policy (for old entity)
            Policy.PolicyUpdate(oldEntity);
            // using validation
            ValidationService.ValidateUpdate(oldEntity, entity);
            // updating entity
            oldEntity.UpdateFrom(entity);
            await Repository.UpdateAsync(oldEntity);
            // retrieving entity (force) from database
            return oldEntity;
        }
        public virtual async Task DeleteAsync(TPrimaryKey id)
        {
            TEntity entity = await RetrieveAsync(id);
            // loading relations for entity
            LoadEntityRelations(entity, DomainMethodNames.Delete);
            // using policy
            Policy.PolicyDelete(entity);
            // using validation
            ValidationService.ValidateDelete(entity);
            // deleting from database
            await Repository.DeleteAsync(entity);
            // deleting composed (entities that are depending on this one) entities
            await CascadeDeleteActionAsync(entity);
        }

        #region Helpers

        protected abstract Expression<Func<TEntity, object>>[] GetRetrieveAllEntityIncludes();
        protected abstract Expression<Func<TEntity, object>>[] GetRetrieveEntityIncludes();
        protected abstract Task CascadeDeleteActionAsync(TEntity entity);
        internal abstract IEnumerable<LoadEntityRelationAction<TEntity>> GetLoadEntityRelationActions();

        protected void LoadEntityRelations(TEntity entity, DomainMethodNames method)
        {
            try
            {
                LoadEntityRelationActions
                    .Where(r => r.ContainsMethod(method))
                    .Select(r => r.ActionExpression)
                    .ToList()
                    .ForEach(action => action(entity));
            }
            catch
            {
                throw new ValidateEntityRelationsException();
            }
        }
        protected virtual IQueryable<TEntity> ApplySortingToQuery(IQueryable<TEntity> query, string sorting)
        {
            return !String.IsNullOrEmpty(sorting) ? query.OrderBy(sorting) : query.OrderBy(r => r.Id);
        }
        protected virtual IQueryable<TEntity> ApplyPolicyAndFilterToQuery(IQueryable<TEntity> entitySet, Expression<Func<TEntity, bool>> filter)
        {
            // using policy filter
            IQueryable<TEntity> query = Policy.RetrieveAllFilter(entitySet);
            // applying filters
            if (filter != null)
            {
                query = query.Where(filter);
            }
            return query;
        }

        #endregion
    }

    internal class LoadEntityRelationAction<TEntity>
    {
        public Action<TEntity> ActionExpression { get; private set; }
        public ISet<DomainMethodNames> MethodsToUse { get; private set; }

        #region Ctors

        public LoadEntityRelationAction(Action<TEntity> actionExpression)
            : this(actionExpression, new HashSet<DomainMethodNames>())
        { }

        public LoadEntityRelationAction(Action<TEntity> actionExpression, ISet<DomainMethodNames> methodsToUse)
        {
            ActionExpression = actionExpression;
            MethodsToUse = methodsToUse;
        }

        #endregion

        public bool ContainsMethod(DomainMethodNames methodName)
        {
            return MethodsToUse.Contains(methodName);
        }
        public void AddMethod(DomainMethodNames methodName)
        {
            MethodsToUse.Add(methodName);
        }
        public void RemoveMethod(DomainMethodNames methodName)
        {
            MethodsToUse.Remove(methodName);
        }
    }
}