using QuantumLogic.Core.Domain.Services;
using QuantumLogic.Core.Utils.ContentManager;
using QuantumLogic.Core.Utils.Export.Entity.DataModels;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;

namespace QuantumLogic.Core.Utils.Export.DataModels.Entity
{
    public interface IEntityExportSettings<TEntity>
    {
        IOperateWithManyEntitiesDomainService<TEntity> OperateWithManyEntitiesService { get; }
        IContentManager ContentManager { get; }
        IEnumerable<IEntityPropertyMapper<TEntity>> PropertyMappers { get; }
        Expression<Func<TEntity, bool>> Filter { get; }
        string Sorting { get; }
        uint? Skip { get; }
        uint? Take { get; }
        string FileName { get; }
        string FileExtension { get; }
        string FileContentType { get; }
    }
}
