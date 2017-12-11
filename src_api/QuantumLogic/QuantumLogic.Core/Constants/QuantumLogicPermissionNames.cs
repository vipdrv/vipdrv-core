using System;
using System.Collections.Generic;
using System.Linq;
using System.Reflection;
using System.Text;

namespace QuantumLogic.Core.Constants
{
    public class QuantumLogicPermissionNames
    {
        #region QuantumLogic menu visibility permissions

        public const string CanViewMenuHome = "CanViewMenuHome";
        public const string CanViewMenuSites = "CanViewMenuSites";
        public const string CanViewMenuLeads = "CanViewMenuLeads";
        public const string CanViewMenuSettings = "CanViewMenuSettings";

        #endregion

        #region Global QuantumLogic permission names

        public const string CanAllAll = "CanAllAll";
        public const string CanRetrieve = "CanRetrieveAll";
        public const string CanCreate = "CanCreateAll";
        public const string CanUpdate = "CanUpdateAll";
        public const string CanDelete = "CanDeleteAll";

        #endregion

        #region MainModule QuantumLogic permission names

        public const string CanAllRole = "CanAllRole";
        public const string CanRetrieveRole = "CanRetrieveRole";
        public const string CanCreateRole = "CanCreateRole";
        public const string CanUpdateRole = "CanUpdateRole";
        public const string CanDeleteRole = "CanDeleteRole";

        #endregion

        #region WidgetModule QuantumLogic permission names

        public const string CanAllSite = "CanAllSite";
        public const string CanRetrieveSite = "CanRetrieveSite";
        public const string CanCreateSite = "CanCreateSite";
        public const string CanUpdateSite = "CanUpdateSite";
        public const string CanDeleteSite = "CanDeleteSite";
        public const string CanUpdateOwnSite = "CanUpdateOwnSite";
        public const string CanDeleteOwnSite = "CanDeleteOwnSite";

        public const string CanAllExpert = "CanAllExpert";
        public const string CanRetrieveExpert = "CanRetrieveExpert";
        public const string CanCreateExpert = "CanCreateExpert";
        public const string CanUpdateExpert = "CanUpdateExpert";
        public const string CanDeleteExpert = "CanDeleteExpert";
        public const string CanUpdateOwnExpert = "CanUpdateOwnExpert";
        public const string CanDeleteOwnExpert = "CanDeleteOwnExpert";

        #endregion

        /// <summary>
        /// Is used to get all permission names
        /// </summary>
        /// <returns>all permission names</returns>
        public static IList<string> GetAllPermission()
        {
            return typeof(QuantumLogicPermissionNames).
                GetFields(BindingFlags.Public | BindingFlags.Static | BindingFlags.FlattenHierarchy)
                .Where(r => r.IsLiteral || !r.IsInitOnly).Select(r => r.GetValue(null)).Cast<string>().ToList();
        }
    }
}
