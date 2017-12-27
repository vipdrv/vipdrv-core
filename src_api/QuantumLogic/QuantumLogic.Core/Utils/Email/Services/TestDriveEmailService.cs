using QuantumLogic.Core.Utils.Email.Templates;
using SendGrid.Helpers.Mail;
using System;
using System.Collections.Generic;
using System.Net;
using QuantumLogic.Core.Utils.ADFGenerator;

namespace QuantumLogic.Core.Utils.Email.Services
{
    public class TestDriveEmailService : ITestDriveEmailService
    {
        protected const string CompleteBookingSubject = "You Upcoming Test Drive";
        protected const string NewLeadNotificationSubject = "New Lead!";
        protected const string DealerInvitationSubject = "Welcome to TetsDrive";
        protected const string AdfEmailSubject = "VIPdrv - ADF XML ";
        protected static EmailAddress EmailFrom = new EmailAddress("no-reply@vipdrv.com", "VIPdrv - VIP Test Drive");
        protected IEmailProvider EmailProvider { get; private set; }

        public TestDriveEmailService(IEmailProvider emailProvider)
        {
            EmailProvider = emailProvider;
        }

        public HttpStatusCode SendTestDriveEmail(EmailAddress emailTo, string subject, IEmailTemplate emailTemplate)
        {
            if (emailTo == null)
            {
                throw new ArgumentException(nameof(emailTo));
            }
            if (emailTemplate == null)
            {
                throw new ArgumentException(nameof(emailTemplate));
            }

            return EmailProvider.SendEmail(emailTo, EmailFrom, subject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
        }

        public HttpStatusCode SendDealerInvitationEmail(EmailAddress emailTo, IEmailTemplate emailTemplate)
        {
            return EmailProvider.SendEmail(emailTo, EmailFrom, DealerInvitationSubject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
        }

        public HttpStatusCode SendCompleteBookingEmail(EmailAddress emailTo, IEmailTemplate emailTemplate)
        {
            return EmailProvider.SendEmail(emailTo, EmailFrom, CompleteBookingSubject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
        }

        public HttpStatusCode SendNewLeadNotificationEmail(EmailAddress emailTo, IEmailTemplate emailTemplate)
        {
            return EmailProvider.SendEmail(emailTo, EmailFrom, NewLeadNotificationSubject, emailTemplate.AsPlainText(), emailTemplate.AsHtml());
        }

        public HttpStatusCode SendAdfEmail(EmailAddress emailTo, IAdfTemplate adfTemplate)
        {
            return EmailProvider.SendEmail(emailTo, EmailFrom, AdfEmailSubject, adfTemplate.AsString(), adfTemplate.AsString());
        }
    }
}
