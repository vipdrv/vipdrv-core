using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class AddedWidgetAsSeparatePageUrl : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "Trim",
                table: "Vehicle",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "WidgetAsSeparatePageUrl",
                table: "Site",
                nullable: true);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "Trim",
                table: "Vehicle");

            migrationBuilder.DropColumn(
                name: "WidgetAsSeparatePageUrl",
                table: "Site");
        }
    }
}
