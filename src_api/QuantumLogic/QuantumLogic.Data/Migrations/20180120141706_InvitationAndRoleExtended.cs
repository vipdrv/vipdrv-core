using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class InvitationAndRoleExtended : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<bool>(
                name: "CanBeUsedForInvitation",
                table: "Role",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AlterColumn<int>(
                name: "InvitatorId",
                table: "Invitation",
                nullable: true,
                oldClrType: typeof(int));
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CanBeUsedForInvitation",
                table: "Role");

            migrationBuilder.AlterColumn<int>(
                name: "InvitatorId",
                table: "Invitation",
                nullable: false,
                oldClrType: typeof(int),
                oldNullable: true);
        }
    }
}
