using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class ExpertEntityHasBeenExtended : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "Email",
                table: "Expert",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "PhoneNumber",
                table: "Expert",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "Title",
                table: "Expert",
                nullable: true);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "Email",
                table: "Expert");

            migrationBuilder.DropColumn(
                name: "PhoneNumber",
                table: "Expert");

            migrationBuilder.DropColumn(
                name: "Title",
                table: "Expert");
        }
    }
}
