using QuantumLogic.Core.Domain.Entities;

namespace QuantumLogic.Core.Domain.Services
{
    /// <summary>
    /// Is used like domain service (operations facade) for entity
    /// </summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    /// <typeparam name="TPrimaryKey">type of entity primary key</typeparam>
    public interface IEntityDomainService<TEntity, TPrimaryKey> :
        IEntityCRUDDomainService<TEntity, TPrimaryKey>,
        IOperateWithManyEntitiesDomainService<TEntity, TPrimaryKey>
        where TEntity : class, IEntity<TPrimaryKey>
    { }
}
