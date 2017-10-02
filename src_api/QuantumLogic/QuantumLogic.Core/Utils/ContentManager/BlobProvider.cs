using System;
using System.IO;
using System.Threading.Tasks;
using Microsoft.WindowsAzure.Storage;
using Microsoft.WindowsAzure.Storage.Blob;

namespace QuantumLogic.Core.Utils.ContentManager
{
    public class BlobProvider : IContentManager
    {
        private readonly CloudBlobClient _blobClient;

        public BlobProvider()
        {
            // TODO: move storage credentials to config file
            var storageAccount = new CloudStorageAccount(
                new Microsoft.WindowsAzure.Storage.Auth.StorageCredentials(
                    "generalstandart256",
                    "ynoR7rDt9T1VRXhpuxfFLg70ySvHOSi6Eq6pOEyqacHyX4YkmLuoZuB66gjhn2b2xrvfoRtbe+iNXfeeSnmePg=="), true);
            _blobClient = storageAccount.CreateCloudBlobClient();
        }

        public async Task<Uri> SaveFileToStorage(FileStream fileStream, string fileName, string contentType)
        {
            CloudBlobContainer container = _blobClient.GetContainerReference("image-container");

            // Get a reference to a blob
            CloudBlockBlob blockBlob = container.GetBlockBlobReference(fileName);
            blockBlob.Properties.ContentType = contentType;

            await blockBlob.UploadFromStreamAsync(fileStream);
            
            return blockBlob.Uri;
        }
    }
}
