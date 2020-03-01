namespace QuantumLogic.Core.Domain.Entities
{
    public interface IPassivable
    {
        bool IsActive { get; set; }
    }
}
