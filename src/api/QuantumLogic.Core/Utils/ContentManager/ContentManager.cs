using System;
using System.IO;
using System.Threading.Tasks;
using Microsoft.WindowsAzure.Storage;
using Microsoft.WindowsAzure.Storage.Blob;

namespace QuantumLogic.Core.Utils.Storage
{
    public class ContentManager : IContentManager
    {
        protected readonly CloudBlobClient BlobClient;
        protected readonly CloudStorageAccount StorageAccount;
        protected readonly CloudBlobContainer BlobContainer;

        public ContentManager()
        {
            // TODO: move StorageCredentials and ContainerName to appsettings.json
            StorageAccount = new CloudStorageAccount(
                new Microsoft.WindowsAzure.Storage.Auth.StorageCredentials(
                    "doodle",
                    "doodle-passs"), true);
            BlobClient = StorageAccount.CreateCloudBlobClient();
            BlobContainer = BlobClient.GetContainerReference("doodle");
        }

        public async Task<Uri> SaveFile(Stream stream, string fileName, string contentType)
        {
            CloudBlockBlob blockBlob = BlobContainer.GetBlockBlobReference(fileName);
            blockBlob.Properties.ContentType = contentType;
            await blockBlob.UploadFromStreamAsync(stream);
            return blockBlob.Uri;
        }

        public Task<Uri> SaveFile(byte[] content, string blobName, string contentType)
        {
            return SaveFile(new MemoryStream(content), blobName, contentType);
        }
    }
}
