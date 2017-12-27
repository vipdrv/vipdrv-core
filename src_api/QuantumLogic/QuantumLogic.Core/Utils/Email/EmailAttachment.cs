using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.Email
{
    public class EmailAttachment
    {
        public string FileName { get; }
        public string Content { get;  }
        public string Type { get; }

        public EmailAttachment(string fileName, string content, string type)
        {
            FileName = fileName;
            Content = content;
            Type = type;
        }
    }
}
