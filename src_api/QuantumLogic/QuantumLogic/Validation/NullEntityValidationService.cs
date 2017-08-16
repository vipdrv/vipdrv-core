using QuantumLogic.Core.Domain.Entities;

namespace QuantumLogic.WebApi.Validation
{
    /// <summary>
    /// Is used as null (no deny) validation (validation facade) for entity operations
    /// </summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    /// <typeparam name="TPrimaryKey">type of primary key</typeparam>
    public sealed class NullEntityValidationService<TEntity, TPrimaryKey> : EntityValidationService<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, IValidable
    {
        #region Inner validation null (no deny operation) methods

        protected override bool ValidateEntity(TEntity entity, bool throwValidationException)
        {
            return true;
        }

        protected override bool ValidateCreate(TEntity entity, bool throwValidationException)
        {
            return ValidateEntity(entity, throwValidationException);
        }

        protected override bool ValidateUpdate(TEntity oldEntity, TEntity actualEntity, bool throwValidationException)
        {
            return ValidateEntity(actualEntity, throwValidationException);
        }

        protected override bool ValidateDelete(TEntity entity, bool throwValidationException)
        {
            return ValidateEntity(entity, throwValidationException);
        }

        #endregion
    }
}
