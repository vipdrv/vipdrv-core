using System;
using System.IO;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.ContentManager
{
    public interface IContentManager
    {
        Task<Uri> SaveFileToStorage(Stream fileStream, string blobName, string contentType);
    }
}
