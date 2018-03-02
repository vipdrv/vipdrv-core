using System;
using System.Collections.Generic;
using System.Text;
using System.Threading.Tasks;
using NUnit.Framework;
using QuantumLogic.Core.Utils.Email;
using QuantumLogic.Core.Utils.Email.Data;
using QuantumLogic.Core.Utils.Email.Data.Templates;
using SendGrid.Helpers.Mail;

namespace QuantumLogic.Tests.WebApi.Controllers
{
    [TestFixture]
    public sealed class LeadControllerTests
    {
        [Test]
        public void DateTime__ShouChangeTimeZone()
        {
            DateTime original = new DateTime();
            DateTime updated = original.Add(new TimeSpan(5, 0, 0));
            updated = updated.Add(new TimeSpan(0, -120, -1));
        }
    }
}
