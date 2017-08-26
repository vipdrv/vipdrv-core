using QuantumLogic.Core.Domain.Entities;

namespace QuantumLogic.WebApi.DataModels
{
    public interface IEntityDto<TEntity, TPrimaryKey> : IMapable<TEntity>, IShouldNormalize
        where TEntity : class, IEntity<TPrimaryKey>
    {
        TPrimaryKey Id { get; }
    }
}
