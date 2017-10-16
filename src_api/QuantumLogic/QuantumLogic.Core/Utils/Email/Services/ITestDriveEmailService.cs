using SendGrid.Helpers.Mail;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public interface ITestDriveEmailService
    {
        string SendCompleteBookingEmail(EmailAddress emailTo, IEmailTemplate htmlContent);
        string SendNewLeadNotificationEmail(EmailAddress emailTo, IEmailTemplate htmlContent);
    }
}
