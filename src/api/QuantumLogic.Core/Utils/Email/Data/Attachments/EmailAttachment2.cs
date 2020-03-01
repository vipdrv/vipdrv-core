using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.Email.Data.Attachments
{
    public class EmailAttachment2 : IEmailAttachment
    {
        public string FileName { get; }
        public string Content { get; }
        public string Type { get; }

        public EmailAttachment2(string fileName, string content, string type)
        {
            FileName = fileName;
            Content = content;
            Type = type;
        }
    }
}
