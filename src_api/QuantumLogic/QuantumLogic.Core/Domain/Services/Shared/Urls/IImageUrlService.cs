using System.Threading.Tasks;

namespace QuantumLogic.Core.Domain.Services.Shared.Urls
{
    public interface IImageUrlService
    {
        /// <summary>
        /// Is used to transform image url via application domain rules:
        ///  - if url is base64 data image it should be uploaded to external resource and new url returned
        ///  - otherwise returns inputed url 
        /// </summary>
        /// <param name="url">image url to transform</param>
        /// <returns>transformed url</returns>
        Task<string> Transform(string url);
        /// <summary>
        /// Is used to remove image via url
        /// </summary>
        /// <param name="url">image url</param>
        /// <returns>action task</returns>
        Task RemoveAsync(string url);
    }
}
