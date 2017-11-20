using System;
using System.Collections.Generic;
using System.Net;
using System.Text;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email
{
    public interface IEmailProvider
    {
        /// <summary>
        /// Is used to send email
        /// </summary>
        /// <param name="emailTo"></param>
        /// <param name="emailFrom"></param>
        /// <param name="subject"></param>
        /// <param name="plainTextContent"></param>
        /// <param name="htmlContent"></param>
        /// <returns></returns>
        HttpStatusCode SendEmail(EmailAddress emailTo, EmailAddress emailFrom, string subject, string plainTextContent, string htmlContent);

        /// <summary>
        /// Is used to send emails to group of recipients
        /// </summary>
        /// <param name="emailTo"></param>
        /// <param name="emailFrom"></param>
        /// <param name="subject"></param>
        /// <param name="plainTextContent"></param>
        /// <param name="htmlContent"></param>
        /// <returns></returns>
        HttpStatusCode SendEmail(IList<EmailAddress> emailTo, EmailAddress emailFrom, string subject, string plainTextContent, string htmlContent);
    }
}

