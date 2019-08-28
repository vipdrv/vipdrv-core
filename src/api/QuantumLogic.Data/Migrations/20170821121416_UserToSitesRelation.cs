using System;
using System.Collections.Generic;
using Microsoft.EntityFrameworkCore.Migrations;

namespace QuantumLogic.Data.Migrations
{
    public partial class UserToSitesRelation : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateIndex(
                name: "IX_Site_UserId",
                table: "Site",
                column: "UserId");

            migrationBuilder.AddForeignKey(
                name: "FK_Site_User_UserId",
                table: "Site",
                column: "UserId",
                principalTable: "User",
                principalColumn: "Id",
                onDelete: ReferentialAction.Cascade);
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Site_User_UserId",
                table: "Site");

            migrationBuilder.DropIndex(
                name: "IX_Site_UserId",
                table: "Site");
        }
    }
}
