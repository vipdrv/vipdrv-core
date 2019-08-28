using QuantumLogic.Core.Domain.Entities;

namespace QuantumLogic.WebApi.Validation
{
    public class NullEntityExtendedValidationService<TEntity, TPrimaryKey> : NullEntityValidationService<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IValidable, IPassivable, IOrderable
    {
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

        #region Inner validation null (no deny operation) methods

        protected bool ValidateChangeActivity(TEntity entity, bool throwValidationException)
        {
            return true;
        }
        protected bool ValidateChangeOrder(TEntity entity, bool throwValidationException)
        {
            return true;
        }

        #endregion
    }
}
