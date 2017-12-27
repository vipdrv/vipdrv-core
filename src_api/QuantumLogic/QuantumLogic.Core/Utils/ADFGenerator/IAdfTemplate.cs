using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.ADFGenerator
{
    public interface IAdfTemplate
    {
        string AsString();
        string AsBase64();
    }
}
