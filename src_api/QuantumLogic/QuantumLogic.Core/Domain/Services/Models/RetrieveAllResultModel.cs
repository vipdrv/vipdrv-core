using System.Collections.Generic;

namespace QuantumLogic.Core.Domain.Services.Models
{
    public sealed class RetrieveAllResultModel<TEntity>
    {
        public IList<TEntity> Entities { get; private set; }
        public int TotalCount { get; private set; }

        #region Ctors

        public RetrieveAllResultModel(IList<TEntity> entities, int totalCount)
        {
            Entities = entities;
            TotalCount = totalCount;
        }

        #endregion
    }
}
