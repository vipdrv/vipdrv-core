using QuantumLogic.Core.Domain.Entities;

namespace QuantumLogic.Core.Utils.Import.Entity
{
    public interface IEntityImportService<TEntity, TKey> : IImportService<TEntity>
        where TEntity : IEntity<TKey>
    { }
}
