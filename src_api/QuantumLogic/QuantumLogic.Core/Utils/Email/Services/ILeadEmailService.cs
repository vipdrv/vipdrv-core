using System.Net;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public interface ILeadEmailService : ITestDriveEmailService
    {
        HttpStatusCode SendNewLeadNotificationEmail(EmailAddress emailTo, IEmailTemplate htmlContent, string plainTextContent = null);
    }
}