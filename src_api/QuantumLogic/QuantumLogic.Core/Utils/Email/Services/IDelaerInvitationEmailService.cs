using System;
using System.Collections.Generic;
using System.Net;
using System.Text;
using QuantumLogic.Core.Utils.Email.Templates;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public interface IDelaerInvitationEmailService
    {
        HttpStatusCode SendDealerInvitationEmail(EmailAddress emailTo, IDealerInvitationEmailTemplate htmlContent, string plainTextContent = null);
    }
}
