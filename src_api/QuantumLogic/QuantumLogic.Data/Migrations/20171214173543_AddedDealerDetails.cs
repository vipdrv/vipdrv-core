using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class AddedDealerDetails : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "Contacts",
                table: "Site",
                newName: "NotificationContacts");

            migrationBuilder.AddColumn<string>(
                name: "DealerAddress",
                table: "Site",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "DealerName",
                table: "Site",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "DealerPhone",
                table: "Site",
                nullable: true);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "DealerAddress",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "DealerName",
                table: "Site");

            migrationBuilder.DropColumn(
                name: "DealerPhone",
                table: "Site");

            migrationBuilder.RenameColumn(
                name: "NotificationContacts",
                table: "Site",
                newName: "Contacts");
        }
    }
}
