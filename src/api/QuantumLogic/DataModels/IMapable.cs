namespace QuantumLogic.WebApi.DataModels
{
    public interface IMapable<TEntity>
    {
        TEntity MapToEntity();
        void MapFromEntity(TEntity entity);
    }
}
