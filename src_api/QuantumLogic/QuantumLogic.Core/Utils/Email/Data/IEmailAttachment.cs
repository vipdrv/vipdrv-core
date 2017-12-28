using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.Email.Data
{
    public interface IEmailAttachment
    {
        string FileName { get; }
        string Content { get; }
        string Type { get; }
    }
}
