namespace QuantumLogic.Core.Domain.Entities
{
    /// <summary>
    /// Is used as base class for all QuantumLogic entities
    /// </summary>
    /// <typeparam name="TPrimaryKey">type of primary key</typeparam>
    public class Entity<TPrimaryKey> : IEntity<TPrimaryKey>
    {
        public TPrimaryKey Id { get; set; }

        #region Ctors

        public Entity()
        { }

        public Entity(TPrimaryKey id)
            : this()
        {
            Id = id;
        }

        #endregion
    }
}
