using System;
using System.Collections.Generic;
using System.Text;

namespace QuantumLogic.Core.Utils.Ftp
{
    public interface IFtpService
    {
        /// <summary>
        /// Is used to get path to last modified file in folder on FTP 
        /// </summary>
        /// <param name="folderPath">email address to send</param>
        /// <returns>Returns String file path</returns>
        string LastModidiedFileInFolder(string folderPath);
        /// <summary>
        /// Is used to download file to specific path
        /// </summary>
        /// <param name="localPath">local path to download file to</param>
        /// <param name="overwrite">override local file</param>
        /// <returns>Returns bool indicator of file is downloaded</returns>
        bool DownloadFile(string localPath, string remotePath, bool overwrite = true);
    }
}
