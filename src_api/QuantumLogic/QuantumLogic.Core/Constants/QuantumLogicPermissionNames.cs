using System;
using System.Collections.Generic;
using System.Linq;
using System.Reflection;
using System.Text;

namespace QuantumLogic.Core.Constants
{
    public class QuantumLogicPermissionNames
    {
        #region Global QuantumLogic permission names

        public const string CanAllAll = "CanAllAll";
        public const string CanRetrieveAll = "CanRetrieveAll";
        public const string CanCreateAll = "CanCreateAll";
        public const string CanUpdateAll = "CanUpdateAll";
        public const string CanDeleteAll = "CanDeleteAll";

        public const string CanAllOwn = "CanAllOwn";

        #endregion

        #region MainModule QuantumLogic permission names

        public const string CanAllUser = "CanAllUser";
        public const string CanRetrieveUser = "CanRetrieveUser";
        public const string CanCreateUser = "CanCreateUser";
        public const string CanUpdateUser = "CanUpdateUser";
        public const string CanDeleteUser = "CanDeleteUser";
        public const string CanRetrieveOwnUser = "CanRetrieveOwnUser";
        public const string CanUpdateOwnUser = "CanUpdateOwnUser";

        public const string CanAllInvitation = "CanAllInvitation";
        public const string CanRetrieveInvitation = "CanRetrieveInvitation";
        public const string CanCreateInvitation = "CanCreateInvitation";
        public const string CanUpdateInvitation = "CanUpdateInvitation";
        public const string CanDeleteInvitation = "CanDeleteInvitation";

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

        public const string CanAllBeverage = "CanAllBeverage";
        public const string CanRetrieveBeverage = "CanRetrieveBeverage";
        public const string CanCreateBeverage = "CanCreateBeverage";
        public const string CanUpdateBeverage = "CanUpdateBeverage";
        public const string CanDeleteBeverage = "CanDeleteBeverage";
        public const string CanUpdateOwnBeverage = "CanUpdateOwnBeverage";
        public const string CanDeleteOwnBeverage = "CanDeleteOwnBeverage";

        public const string CanAllRoute = "CanAllRoute";
        public const string CanRetrieveRoute = "CanRetrieveRoute";
        public const string CanCreateRoute = "CanCreateRoute";
        public const string CanUpdateRoute = "CanUpdateRoute";
        public const string CanDeleteRoute = "CanDeleteRoute";
        public const string CanUpdateOwnRoute = "CanUpdateOwnRoute";
        public const string CanDeleteOwnRoute = "CanDeleteOwnRoute";

        public const string CanAllLead = "CanAllLead";
        public const string CanRetrieveLead = "CanRetrieveLead";
        public const string CanCreateLead = "CanCreateLead";
        public const string CanUpdateLead = "CanUpdateLead";
        public const string CanDeleteLead = "CanDeleteLead";
        public const string CanRetrieveOwnLead = "CanRetrieveOwnLead";
        public const string CanUpdateOwnLead = "CanUpdateOwnLead";
        public const string CanDeleteOwnLead = "CanDeleteOwnLead";

        #endregion

        #region QuantumLogic menu visibility permissions

        public const string CanViewMenuHome = "CanViewMenuHome";
        public const string CanViewMenuSites = "CanViewMenuSites";
        public const string CanViewMenuLeads = "CanViewMenuLeads";
        public const string CanViewMenuSettings = "CanViewMenuSettings";

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
