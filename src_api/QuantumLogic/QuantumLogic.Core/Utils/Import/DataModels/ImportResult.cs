using System;
using System.Collections.Generic;

namespace QuantumLogic.Core.Utils.Import.DataModels
{
    public abstract class ImportResult<T> : IImportResult<T>
    {
        public IEnumerable<T> Data { get; private set; }

        public TimeSpan ElapsedTime { get; private set; }

        #region Ctors

        public ImportResult(IEnumerable<T> data, TimeSpan elapsedTime)
        {
            Data = data ?? new List<T>();
            ElapsedTime = elapsedTime;
        }

        #endregion
    }
}
