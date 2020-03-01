using QuantumLogic.Core.Domain.Entities;
using QuantumLogic.Core.Domain.Validation;

namespace QuantumLogic.WebApi.Validation
{
    public abstract class EntityExtendedValidationService<TEntity, TPrimaryKey> : EntityValidationService<TEntity, TPrimaryKey>, IEntityExtendedValidationService<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IValidable, IPassivable, IOrderable
    {
        #region Ctors

        public EntityExtendedValidationService()
            : base()
        { }

        #endregion

        public void ValidateChangeActivity(TEntity entity)
        {
            ValidateChangeActivity(entity, true);
        }
        public bool IsValidChangeActivity(TEntity entity)
        {
            return ValidateChangeActivity(entity, false);
        }
        public void ValidateChangeOrder(TEntity entity)
        {
            ValidateChangeOrder(entity, true);
        }
        public bool IsValidChangeOrder(TEntity entity)
        {
            return ValidateChangeOrder(entity, false);
        }

        #region Inner validation methods

        protected abstract bool ValidateChangeActivity(TEntity entity, bool throwValidationException);
        protected abstract bool ValidateChangeOrder(TEntity entity, bool throwValidationException);

        #endregion
    }
}
