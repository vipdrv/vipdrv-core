using OfficeOpenXml;
using QuantumLogic.Core.Domain.Context;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Core.Domain.Policy.WidgetModule;
using QuantumLogic.Core.Domain.Repositories.WidgetModule;
using QuantumLogic.Core.Domain.Validation.Widget;
using QuantumLogic.Core.Extensions.DateTimeEx;
using QuantumLogic.Core.Utils.ContentManager;
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Widget.Leads
{
    public class LeadDomainService : EntityDomainService<Lead, int>, ILeadDomainService
    {
        #region Injected dependencies

        public IContentManager ContentManager { get; private set; }

        #endregion

        #region Ctors

        public LeadDomainService(IDomainContext domainContext, ILeadRepository repository, ILeadPolicy policy, ILeadValidationService validationService, IContentManager contentManager)
            : base(domainContext, repository, policy, validationService)
        {
            ContentManager = contentManager;
        }

        #endregion

        public override Task<Lead> CreateAsync(Lead entity)
        {
            entity.RecievedUtc = DateTime.UtcNow;
            return base.CreateAsync(entity);
        }

        #region Excel export (good to refactor this and make universal for using with any entity)

        public async Task<string> ExportDataToExcelAsync(
            string fileName, string worksheetsName, TimeSpan timeZoneOffset,
            Expression<Func<Lead, bool>> filter = null, 
            string sorting = null, 
            int skip = 0, int take = 0)
        {
            List<EntityOptionConfig<Lead>> entityOptions = new List<EntityOptionConfig<Lead>>()
            {
                //new EntityOptionConfig<Lead>("Id", LocalizeDisplayName("Id"), (r => r.Id), false),
                new EntityOptionConfig<Lead>("FirstName", LocalizeDisplayName("First name"), (r => r.FirstName), true),
                new EntityOptionConfig<Lead>("SecondName", LocalizeDisplayName("Second name"), (r => r.SecondName), true),
                new EntityOptionConfig<Lead>("Site", LocalizeDisplayName("Site"), (r => r.Site.Name), true),
                new EntityOptionConfig<Lead>("RecievedUtc", LocalizeDisplayName("Recieved"), (r => r.RecievedUtc.FormatUtcDateTimeToUserFriendlyString(timeZoneOffset)), true),
                new EntityOptionConfig<Lead>("Expert", LocalizeDisplayName("Expert"), (r => r.Expert.Name), true),
                new EntityOptionConfig<Lead>("Route", LocalizeDisplayName("Route"), (r => r.Route.Name), true),
                new EntityOptionConfig<Lead>("Beverage", LocalizeDisplayName("Beverage"), (r => r.Beverage.Name), true),
                new EntityOptionConfig<Lead>("UserEmail", LocalizeDisplayName("Email"), (r => r.UserEmail), true),
                new EntityOptionConfig<Lead>("UserPhone", LocalizeDisplayName("Phone"), (r => r.UserPhone), true)
            };
            string fileUrl;
            var entities = (await RetrieveAllAsync(filter, sorting, skip, take)).Entities;
            using (ExcelPackage pck = new ExcelPackage())
            {
                ExcelWorksheet workSheet = pck.Workbook.Worksheets.Add(worksheetsName);

                int workSheetRow = 1;
                int workSheetCell = 1;

                foreach (var item in entityOptions.Select(r => r.DisplayName))
                {
                    workSheet.Cells[workSheetRow, workSheetCell].Value = item;
                    workSheetCell++;
                }
                workSheetRow++;
                workSheetCell = 1;

                foreach (var item in entities)
                {
                    foreach (var action in entityOptions.Select(r => r.Action))
                    {
                        object value = TryGetValueOrReturnDefault<Lead, int>(item, action);
                        workSheet.Cells[workSheetRow, workSheetCell].Value = value;
                        workSheetCell++;
                    }
                    workSheetRow++;
                    workSheetCell = 1;
                }
                fileUrl = (await ContentManager.SaveFileToStorage(new MemoryStream(pck.GetAsByteArray()), fileName + ".xlsx", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")).ToString();
            }
            return fileUrl;
        }

        private object TryGetValueOrReturnDefault<TEntity, TPrimaryKey>(TEntity item, Func<TEntity, object> action)
        {
            try
            {
                var value = action(item);
                return value;
            }
            catch
            {
                return string.Empty;
            }
        }

        private string LocalizeDisplayName(string v)
        {
            /// localization can be added here
            return v;
        }

        #endregion

        protected override Task CascadeDeleteActionAsync(Lead entity)
        {
            return Task.CompletedTask;
        }
        protected override IEnumerable<LoadEntityRelationAction<Lead>> GetLoadEntityRelationActions()
        {
            return new List<LoadEntityRelationAction<Lead>>();
        }
        protected override Expression<Func<Lead, object>>[] GetRetrieveAllEntityIncludes()
        {
            return new List<Expression<Func<Lead, object>>>()
            {
                entity => entity.Beverage,
                entity => entity.Expert,
                entity => entity.Route,
                entity => entity.Site
            }
            .ToArray();
        }
        protected override Expression<Func<Lead, object>>[] GetRetrieveEntityIncludes()
        {
            return new List<Expression<Func<Lead, object>>>()
            {
                entity => entity.Beverage,
                entity => entity.Expert,
                entity => entity.Route,
                entity => entity.Site
            }
            .ToArray();
        }
    }
}
