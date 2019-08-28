namespace QuantumLogic.Core.Domain.Entities
{
    public interface IUpdatableFrom<TEntity>
    {
        void UpdateFrom(TEntity actualEntity);
    }
}
