using FluentFTP;
using QuantumLogic.Core.Shared.Factories;
using System.Net;

namespace QuantumLogic.Core.Domain.Services.Widget.Vehicles.Import.Factories
{
    public class VehicleImportFtpClientInstantFactory : IInstantFactory<IFtpClient>
    {
        public IFtpClient Create()
        {
            return new FtpClient("ftp://ftp.testdrive.pw")
            {
                Credentials = new NetworkCredential("root", "6gfb9P3xE2jAw7Sd")
            };
        }
    }
}
