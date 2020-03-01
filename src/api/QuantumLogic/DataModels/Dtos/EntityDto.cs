using QuantumLogic.Core.Domain.Entities;

namespace QuantumLogic.WebApi.DataModels.Dtos
{
    public abstract class EntityDto<TEntity, TPrimaryKey> : IEntityDto<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>, new()
    {
        public TPrimaryKey Id { get; set; }

        public virtual void MapFromEntity(TEntity entity)
        {
            Id = entity.Id;
        }
        public virtual TEntity MapToEntity()
        {
            return new TEntity()
            {
                Id = Id
            };
        }
        public abstract void NormalizeAsRequest();
        public abstract void NormalizeAsResponse();
    }
}
