using QuantumLogic.Core.Utils.Storage;
using System;
using System.Linq;
using System.Text.RegularExpressions;
using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Shared.Urls
{
    public class ImageUrlService : IImageUrlService
    {
        #region Static

        public static readonly Regex IsDataBase64Regex = new Regex("data:image\\/([a-zA-Z]*);base64,([^\\\"]*)");

        #endregion

        #region Injected dependencies

        protected readonly IContentManager ContentManager;

        #endregion

        #region Ctors

        public ImageUrlService(IContentManager contentManager)
        {
            ContentManager = contentManager;
        }

        #endregion

        public virtual async Task RemoveAsync(string url)
        {
            try
            {
                // not need to implement yet
            }
            catch
            {
                // should not throw any exception
            }
        }

        public virtual async Task<string> Transform(string url)
        {
            string transformedUrl;
            try
            {
                if (IsDataBase64Regex.IsMatch(url))
                {
                    transformedUrl = await TransformAsDataBase64(url);
                }
                else
                {
                    transformedUrl = url;
                }
            }
            catch
            {
                transformedUrl = url;
            }
            return transformedUrl;
        }

        #region Helpers

        protected virtual async Task<string> TransformAsDataBase64(string url)
        {
            string[] contentParts = url.Split(new string[] { ";base64," }, StringSplitOptions.None);
            return (await ContentManager
                    .SaveFile(
                        Convert.FromBase64String(contentParts.Last()), 
                        Guid.NewGuid().ToString(), 
                        contentParts.First().Split(':').Last()))
                .AbsoluteUri;
        }

        #endregion
    }
}
