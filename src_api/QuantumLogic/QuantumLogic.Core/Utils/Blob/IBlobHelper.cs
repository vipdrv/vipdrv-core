using System;
using System.Collections.Generic;
using System.IO;
using System.Text;

namespace QuantumLogic.Core.Utils.Blob
{
    interface IBlobHelper
    {
        string UploadFileToBlob(string fileName, Stream fileStream);
    }
}
