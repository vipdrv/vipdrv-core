namespace QuantumLogic.Core.Shared.Factories
{
    public interface IFactory<out TEntity, in TSettings>
    {
        TEntity Create(TSettings settings);
    }
}
