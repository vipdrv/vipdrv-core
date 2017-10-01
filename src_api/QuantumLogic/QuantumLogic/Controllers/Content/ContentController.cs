using Microsoft.AspNetCore.Mvc;
using System;
using System.IO;
using System.Net;
using System.Text.RegularExpressions;
using System.Threading.Tasks;

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
            return Task.FromResult(String.Empty);
            // TODO: replace stub with real implementation 
#warning stub realization that should be removed
            string imageUrl = null;
            try
            {
                if (request != null && !String.IsNullOrEmpty(request.Img))
                {
                    //string imageDataURL = string.Format("data:image/png;base64,{0}", request.Img);
                    string path;
                    string filename;
                    //string stringName = imageDataURL;

                    var base64Data = Regex.Match(request.Img, @"data:image/(?<type>.+?),(?<data>.+)").Groups["data"].Value;
                    var binData = Convert.FromBase64String(base64Data);
                    path = "physical path";//@"C:\Source\git-repos\Quantum\src_api\QuantumLogic\QuantumLogic\Content\Images";
                    bool created = false;
                    string fileName;
                    while (!created)
                    {
                        fileName = Guid.NewGuid() + ".PNG";
                        filename = Path.Combine(path, fileName);

                        if (!System.IO.File.Exists(filename))
                        {
                            System.IO.File.WriteAllBytes(filename, binData);
                            created = true;
                        }
                    }
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
