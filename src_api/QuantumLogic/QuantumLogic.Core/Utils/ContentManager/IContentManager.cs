using System;
using System.IO;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Utils.Storage
{
    /// <summary>
    /// Is used to operate with files via Azure storage 
    /// </summary>
    public interface IContentManager
    {
        /// <summary>
        /// Is used to save file to storage
        /// </summary>
        /// <param name="fileStream"></param>
        /// <param name="blobName">File name</param>
        /// <param name="contentType">For exmaple: image/jpeg </param>
        /// <returns>
        /// returns Url to created file
        /// </returns>
        Task<Uri> SaveFile(Stream fileStream, string blobName, string contentType);

        /// <summary>
        /// Is used to save file to storage
        /// </summary>
        /// <param name="content"></param>
        /// <param name="blobName"></param>
        /// <param name="contentType"></param>
        /// <returns>
        /// returns Url to created file
        /// </returns>
        Task<Uri> SaveFile(byte[] content, string blobName, string contentType);
    }
}
