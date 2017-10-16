using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class AddedInvitationHistoricalInfo : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<DateTime>(
                name: "CreatedTimeUtc",
                table: "Invitation",
                nullable: false,
                defaultValue: new DateTime(1, 1, 1, 0, 0, 0, 0, DateTimeKind.Unspecified));

            migrationBuilder.AddColumn<bool>(
                name: "Used",
                table: "Invitation",
                nullable: false,
                defaultValue: false);

            migrationBuilder.AddColumn<DateTime>(
                name: "UsedTimeUtc",
                table: "Invitation",
                nullable: true);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CreatedTimeUtc",
                table: "Invitation");

            migrationBuilder.DropColumn(
                name: "Used",
                table: "Invitation");

            migrationBuilder.DropColumn(
                name: "UsedTimeUtc",
                table: "Invitation");
        }
    }
}
