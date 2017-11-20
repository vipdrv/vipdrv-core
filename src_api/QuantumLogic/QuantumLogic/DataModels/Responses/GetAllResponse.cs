using System.Collections.Generic;

namespace QuantumLogic.WebApi.DataModels.Responses
{
    public class GetAllResponse<TEntityDto>
    {
        public IReadOnlyList<TEntityDto> Items { get; private set; }
        public int TotalCount { get; private set; }

        #region Ctors

        public GetAllResponse(List<TEntityDto> items, int totalCount)
        {
            Items = items.AsReadOnly();
            TotalCount = totalCount;
        }

        #endregion
    }
}
