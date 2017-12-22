using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class AddedVdpUrl : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "UserComment",
                table: "Lead",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "VdpUrl",
                table: "Lead",
                nullable: true);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "UserComment",
                table: "Lead");

            migrationBuilder.DropColumn(
                name: "VdpUrl",
                table: "Lead");
        }
    }
}
