using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class AddedVehicleEntity : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "CrmProvider",
                table: "Site",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "FeedFormat",
                table: "Site",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SiteProvider",
                table: "Site",
                nullable: true);

            migrationBuilder.CreateTable(
                name: "Vehicle",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("SqlServer:ValueGenerationStrategy", SqlServerValueGenerationStrategy.IdentityColumn),
                    Condition = table.Column<byte>(nullable: false),
                    ImageUrl = table.Column<string>(nullable: true),
                    Make = table.Column<string>(nullable: true),
                    Model = table.Column<string>(nullable: true),
                    SiteId = table.Column<int>(nullable: false),
                    Stock = table.Column<string>(nullable: true),
                    Title = table.Column<string>(nullable: true),
                    VIN = table.Column<string>(nullable: true),
                    Year = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Vehicle", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Vehicle_Site_SiteId",
                        column: x => x.SiteId,
                        principalTable: "Site",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_Vehicle_SiteId",
                table: "Vehicle",
                column: "SiteId");
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "Vehicle");

            migrationBuilder.DropColumn(
                name: "CrmProvider",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "FeedFormat",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "SiteProvider",
                table: "Site");
        }
    }
}
