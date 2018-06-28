using System;
using System.Collections.Generic;

namespace QuantumLogic.Core.Utils.Import.DataModels
{
    public interface IImportResult<T>
    {
        IEnumerable<T> Data { get; }
        TimeSpan ElapsedTime { get; }
    }
}
