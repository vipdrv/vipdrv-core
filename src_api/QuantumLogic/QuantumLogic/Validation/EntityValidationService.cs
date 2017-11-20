using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Validation;

namespace QuantumLogic.WebApi.Validation
{
    public abstract class EntityValidationService<TEntity, TPrimaryKey> : IEntityValidationService<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IValidable
    {
        #region Ctors

        public EntityValidationService()
        { }

        #endregion

        public void ValidateEntity(TEntity entity)
        {
            ValidateEntity(entity, true);
        }
        public void ValidateCreate(TEntity entity)
        {
            ValidateCreate(entity, true);
        }
        public void ValidateUpdate(TEntity oldEntity, TEntity actualEntity)
        {
            ValidateUpdate(oldEntity, actualEntity, true);
        }
        public void ValidateDelete(TEntity entity)
        {
            ValidateDelete(entity, true);
        }
        public bool IsValidEntity(TEntity entity)
        {
            return ValidateEntity(entity, false);
        }
        public bool IsValidCreate(TEntity entity)
        {
            return ValidateCreate(entity, false);
        }
        public bool IsValidUpdate(TEntity oldEntity, TEntity actualEntity)
        {
            return ValidateUpdate(oldEntity, actualEntity, false);
        }
        public bool IsValidDelete(TEntity entity)
        {
            return ValidateDelete(entity, false);
        }

        #region Inner validation methods

        protected virtual bool ValidateEntity(TEntity entity, bool throwValidationException)
        {
            bool validationResult;
            if (throwValidationException)
            {
                entity.Validate();
                validationResult = true;
            }
            else
            {
                validationResult = entity.IsValid();
            }
            return validationResult;
        }
        protected abstract bool ValidateCreate(TEntity entity, bool throwValidationException);
        protected abstract bool ValidateUpdate(TEntity oldEntity, TEntity actualEntity, bool throwValidationException);
        protected abstract bool ValidateDelete(TEntity entity, bool throwValidationException);

        #endregion
    }
}
