using System;

namespace QuantumLogic.Core.Utils.Export.Entity.DataModels
{
    public interface IEntityPropertyMapper<TEntity>
    {
        string Key { get; }
        string DisplayName { get; }
        Func<TEntity, object> MapAction { get; }
        bool UseByDefault { get; }
    }
}
