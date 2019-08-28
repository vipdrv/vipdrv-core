using QuantumLogic.Core.Utils.Export.Entity.DataModels;
using System;

namespace QuantumLogic.WebApi.Providers.Export.Excel.DataModels
{
    public class EntityPropertyMapper<TEntity> : IEntityPropertyMapper<TEntity>
    {
        public string Key { get; private set; }
        public string DisplayName { get; private set; }
        public Func<TEntity, object> MapAction { get; private set; }
        public bool UseByDefault { get; private set; }

        #region Ctors

        public EntityPropertyMapper(string key, string displayName, Func<TEntity, object> mapAction, bool useByDefault)
        {
            Key = key;
            DisplayName = displayName;
            MapAction = mapAction;
            UseByDefault = useByDefault;
        }

        #endregion
    }
}
