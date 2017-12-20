using System;
using System.IO;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.ContentManager
{
    /// <summary>
    /// Is used to operate with files via Azure storage 
    /// </summary>
    public interface IContentManager
    {
        /// <summary>
        /// Is used to save file to Azure storage with public access
        /// </summary>
        /// <param name="fileStream"></param>
        /// <param name="blobName"></param>
        /// <param name="contentType"></param>
        /// <returns>
        /// returns Url to created file
        /// </returns>
        Task<Uri> SaveFileToStorage(Stream fileStream, string blobName, string contentType);

        /// <summary>
        /// Is used to save file to Azure storage with public access
        /// </summary>
        /// <param name="content"></param>
        /// <param name="blobName"></param>
        /// <param name="contentType"></param>
        /// <returns>
        /// returns Url to created file
        /// </returns>
        Task<Uri> SaveFileToStorage(byte[] content, string blobName, string contentType);
    }
}
