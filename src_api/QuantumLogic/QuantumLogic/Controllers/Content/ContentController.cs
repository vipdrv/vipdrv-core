using Microsoft.AspNetCore.Mvc;
using System;
using System.IO;
using System.Net;
using System.Text.RegularExpressions;
using System.Threading.Tasks;
using QuantumLogic.Core.Utils.ContentManager;
using QuantumLogic.Core.Utils.ContentManager.Providers;

namespace QuantumLogic.WebApi.Controllers.Content
{
    [Route("api/content")]
    public class ContentController : Controller
    {
        [Obsolete]
        public class ImgRequestCrutch
        {
            public string Img { get; set; }
        }

        [HttpPost("image")]
        public Task<string> UploadImageAsync([FromBody]ImgRequestCrutch request)
        {
            string imageUrl = null;
            try
            {
                if (request != null && !String.IsNullOrEmpty(request.Img))
                {
                    var base64Data = Regex.Match(request.Img, @"data:image/(?<type>.+?),(?<data>.+)").Groups["data"].Value;
                    var binData = Convert.FromBase64String(base64Data);
                    MemoryStream memoryStream = new MemoryStream(binData);

                    var fileName = Guid.NewGuid().ToString("N") + ".png";
                    var contentType = "image/png";
                    
                    imageUrl = new BlobProvider().SaveFileToStorage(memoryStream, fileName, contentType).Result.AbsoluteUri;
                }
                else
                {
                    Response.StatusCode = (int)HttpStatusCode.BadRequest;
                }
            }
            catch (Exception)
            {
                Response.StatusCode = (int)HttpStatusCode.InternalServerError;
            }
            return Task.FromResult($"{imageUrl}");
        }
    }
}
