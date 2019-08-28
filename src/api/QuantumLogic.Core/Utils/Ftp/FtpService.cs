using System;
using System.Collections.Generic;
using System.Net;
using System.Text;
using FluentFTP;

namespace QuantumLogic.Core.Utils.Ftp
{
    public class FtpService : IFtpService
    {
        protected readonly FtpClient FtpClient;

        public FtpService()
        {
            //TODO: Move FTP credetials to appsettings.json
            FtpClient = new FtpClient("ftp://ftp.testdrive.pw");
            FtpClient.Credentials = new NetworkCredential("root", "6gfb9P3xE2jAw7Sd");
            FtpClient.Connect();
        }

        public string LastModidiedFileInFolder(string folderPath)
        {
            FtpListItem lastModifiedFile = new FtpListItem();

            if (FtpClient.DirectoryExists(folderPath))
            {
                bool firstFileInFolder = true;
                foreach (FtpListItem item in FtpClient.GetListing(folderPath))
                {
                    if (item.Type == FtpFileSystemObjectType.File)
                    {
                        if (firstFileInFolder)
                        {
                            lastModifiedFile = item;
                            firstFileInFolder = false;
                        }

                        if (item.Modified > lastModifiedFile.Modified)
                        {
                            lastModifiedFile = item;
                        }
                    }
                }
            }

            return lastModifiedFile.FullName;
        }

        public bool DownloadFile(string localPath, string remotePath, bool overwrite = true)
        {
            return FtpClient.DownloadFile(localPath, remotePath, overwrite);
        }
    }
}
