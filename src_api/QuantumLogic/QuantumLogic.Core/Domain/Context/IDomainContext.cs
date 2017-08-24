namespace QuantumLogic.Core.Domain.Context
{
    /// <summary>
    /// Is used like context for domain layer and should be single for one request. 
    /// Contains references to all allowed domain services via <see cref="Lazy{TEntity}"/>.
    /// </summary>
    public interface IDomainContext
    {
        //Lazy<IEntityDomainService> EntityDomainService { get; }
    }
}
