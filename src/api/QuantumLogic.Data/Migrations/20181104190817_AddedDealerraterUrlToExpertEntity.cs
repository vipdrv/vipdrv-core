using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class AddedDealerraterUrlToExpertEntity : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "ShowLocationInfo",
                table: "Lead");

            migrationBuilder.AddColumn<string>(
                name: "DealerraterUrl",
                table: "Expert",
                nullable: true);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "DealerraterUrl",
                table: "Expert");

            migrationBuilder.AddColumn<bool>(
                name: "ShowLocationInfo",
                table: "Lead",
                nullable: false,
                defaultValue: false);
        }
    }
}
