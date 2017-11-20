using QuantumLogic.Core.Domain.Entities;

namespace QuantumLogic.WebApi.DataModels.Dtos
{
    public interface IEntityDto<TEntity, TPrimaryKey> : IMapable<TEntity>, IShouldNormalize
        where TEntity : class, IEntity<TPrimaryKey>
    {
        TPrimaryKey Id { get; }
    }
}
