using QuantumLogic.Core.Domain.Services;
using QuantumLogic.Core.Utils.ContentManager;
using QuantumLogic.Core.Utils.Export.DataModels.Entity;
using QuantumLogic.Core.Utils.Export.Entity.DataModels;
using System;
using System.Collections.Generic;
using System.Linq.Expressions;

namespace QuantumLogic.Core.Utils.Export.Entity.Concrete.Excel.DataModels
{
    public class ExcelExportSettings<TEntity> : IEntityExportSettings<TEntity>
    {
        public IOperateWithManyEntitiesDomainService<TEntity> OperateWithManyEntitiesService { get; private set; }
        public IContentManager ContentManager { get; private set; }
        public IEnumerable<IEntityPropertyMapper<TEntity>> PropertyMappers { get; private set; }
        public Expression<Func<TEntity, bool>> Filter { get; private set; }
        public string Sorting { get; private set; }
        public uint? Skip { get; private set; }
        public uint? Take { get; private set; }
        public string FileName { get; private set; }
        public string FileExtension => ".xlsx";
        public string FileContentType => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
        /// <summary>
        /// Is used like worksheet name
        /// </summary>
        public string WorksheetName { get; private set; }
        /// <summary>
        /// Is used like max take count per request for export (to prevent stack overflow for large entity collections)
        /// </summary>
        public uint MaxTakePerRequest => 1000;

        #region Ctors

        public ExcelExportSettings(
            string fileName,
            string worksheetName,
            IOperateWithManyEntitiesDomainService<TEntity> operateWithManyEntitiesService,
            IContentManager contentManager,
            IEnumerable<IEntityPropertyMapper<TEntity>> propertyMappers,
            Expression<Func<TEntity, bool>> filter,
            string sorting,
            uint? skip = null,
            uint? take = null)
        {
            FileName = fileName;
            WorksheetName = worksheetName;
            OperateWithManyEntitiesService = operateWithManyEntitiesService;
            ContentManager = contentManager;
            PropertyMappers = propertyMappers;
            Filter = filter;
            Sorting = sorting;
            Skip = skip;
            Take = take;
        }

        #endregion
    }
}
