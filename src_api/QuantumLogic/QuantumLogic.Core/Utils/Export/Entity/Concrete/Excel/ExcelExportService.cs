using OfficeOpenXml;
using QuantumLogic.Core.Utils.Export.DataModels.Entity;
using QuantumLogic.Core.Utils.Export.Entity.Concrete.Excel.DataModels;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.Export.Entity.Concrete.Excel
{
    /// <summary>
    /// Is used like export service for Excel
    /// </summary>
    /// <typeparam name="TEntity">type of entity</typeparam>
    public static class ExcelExportService<TEntity>
    {
        /// <summary>
        /// Is used to export data to excel file
        /// </summary>
        /// <param name="settings">export settings</param>
        /// <returns>uri to retrieve file with exported info</returns>
        public static Task<Uri> ExportDataAsync(IEntityExportSettings<TEntity> settings)
        {
            return InnerExportDataAsync(ValidateAndMapSettings(settings));
        }

        #region Helpers

        private static ExcelExportSettings<TEntity> ValidateAndMapSettings(IEntityExportSettings<TEntity> settings)
        {
            ExcelExportSettings<TEntity> excelExportSettings = settings as ExcelExportSettings<TEntity>;
            if (excelExportSettings == null)
            {
                throw new ArgumentException(nameof(settings));
            }
            return excelExportSettings;
        }
        private static async Task<Uri> InnerExportDataAsync(ExcelExportSettings<TEntity> settings)
        {
            Uri fileUri;
            using (ExcelPackage package = new ExcelPackage())
            {
                ExcelWorksheet worksheet = package.Workbook.Worksheets.Add(settings.WorksheetName);
                int workSheetRow = 1;
                int workSheetCell = 1;
                foreach (var item in settings.PropertyMappers.Select(r => r.DisplayName))
                {
                    worksheet.Cells[workSheetRow, workSheetCell].Value = item;
                    workSheetCell++;
                }
                int totalCount = await settings.OperateWithManyEntitiesService.GetTotalCountAsync(settings.Filter);
                int skip = settings.Skip.HasValue ? (int)settings.Skip.Value : 0;
                int take = settings.Take.HasValue ? (int)settings.Take.Value : totalCount - skip;
                IList<Func<TEntity, object>> mapActions = settings.PropertyMappers.Select(r => r.MapAction).ToList();
                while (skip < totalCount && take > 0)
                {
                    int currentRequestTake = take < settings.MaxTakePerRequest ? take : (int)settings.MaxTakePerRequest;
                    IList<TEntity> entities = await settings.OperateWithManyEntitiesService
                        .RetrieveAllAsync(settings.Filter, settings.Sorting, skip, currentRequestTake);
                    foreach (var entity in entities)
                    {
                        workSheetRow++;
                        workSheetCell = 1;
                        foreach (var mapAction in mapActions)
                        {
                            object value = TryGetValueOrReturnDefault(entity, mapAction);
                            worksheet.Cells[workSheetRow, workSheetCell].Value = value;
                            workSheetCell++;
                        }
                    }
                    skip += currentRequestTake;
                    take -= currentRequestTake;
                }
                fileUri = await settings.ContentManager
                    .SaveFile(package.GetAsByteArray(), $"{settings.FileName}{settings.FileExtension}", settings.FileContentType);
            }
            return fileUri;
        }
        private static object TryGetValueOrReturnDefault(TEntity item, Func<TEntity, object> action)
        {
            try
            {
                return action(item);
            }
            catch
            {
                return string.Empty;
            }
        }

        #endregion
    }
}
