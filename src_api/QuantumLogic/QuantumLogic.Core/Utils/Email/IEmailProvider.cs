using System;
using System.Collections.Generic;
using System.Net;
using System.Text;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email
{
    public interface IEmailProvider
    {
        HttpStatusCode SendEmail(EmailAddress emailTo, EmailAddress emailFrom, string subject, string plainTextContent, string htmlContent);
    }
}
