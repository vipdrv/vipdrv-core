namespace QuantumLogic.Core.Domain.Entities.MainModule
{
    public class Claim : IEntity<string>
    {
        public string Id { get; set; }
        public string Name { get; set; }

        #region Ctors

        public Claim()
        { }

        public Claim(string id, string name)
            : this()
        {
            Id = id;
            Name = name;
        }

        #endregion
    }
}
