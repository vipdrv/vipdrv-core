namespace QuantumLogic.Core.Domain.Entities
{
    /// <summary>
    /// Is used as QuantumLogic entity
    /// </summary>
    /// <typeparam name="TPrimaryKey">type of primary key</typeparam>
    public interface IEntity<TPrimaryKey>
    {
        TPrimaryKey Id { get; set; }
    }
}