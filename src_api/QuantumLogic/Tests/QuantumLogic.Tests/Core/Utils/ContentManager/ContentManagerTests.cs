using System.IO;
using System.Threading.Tasks;
using NUnit.Framework;
using QuantumLogic.Core.Utils.ContentManager;
using QuantumLogic.Core.Utils.ContentManager.Providers;

namespace QuantumLogic.Tests.Core.Utils.ContentManager
{
    [TestFixture]
    public sealed class ContentManagerTests
    {
        [Test]
        [Ignore("Real filesystem, Real storage")]
        public async Task SaveBlobFromStream__SlouldWork()
        {
            var blobProvider = new BlobProvider();

            var fileStream = File.OpenRead(@"C:\Users\Ultramarine\Google Drive\Media\Pictures\Test\29312940.jpeg");
            var fileName = "29312940.jpeg";
            var contentType = "image/jpg";

            var imageUrl = await blobProvider.SaveFileToStorage(fileStream, fileName, contentType);

            Assert.AreEqual(imageUrl, $"https://generalstandart256.blob.core.windows.net/image-container/{fileName}");
        }
    }
}
