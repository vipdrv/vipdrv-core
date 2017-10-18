using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.Email.Templates
{
    public interface IDealerInvitationEmailTemplate
    {
        /// <summary>
        /// Is used to create Invitation Email Template for a new Dealer
        /// </summary>
        /// <param name="dealerName"></param>
        /// <param name="invitationLink"></param>
        /// <returns>
        /// HTML template as a string
        /// </returns>
        string GetDealerInvitationEmailTemplate(string dealerName, string invitationLink);
    }
}
