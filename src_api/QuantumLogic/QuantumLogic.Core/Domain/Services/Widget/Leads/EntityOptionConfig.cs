using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Domain.Services.Widget.Leads
{
    public class EntityOptionConfig<TEntity>
    {
        public string Key { get; set; }

        public string DisplayName { get; set; }

        public Func<TEntity, object> Action { get; set; }

        public bool UseByDefault { get; set; }

        #region Ctors

        public EntityOptionConfig()
        {
            Key = null;
            DisplayName = null;
            Action = null;
            UseByDefault = false;
        }

        public EntityOptionConfig(string key, string displayName,
            Func<TEntity, object> action, bool useByDefault = false)
        {
            Key = key;
            DisplayName = displayName;
            Action = action;
            UseByDefault = useByDefault;
        }

        #endregion
    }
}
