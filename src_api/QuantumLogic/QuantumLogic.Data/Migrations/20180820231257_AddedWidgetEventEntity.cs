using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;
using System;

namespace QuantumLogic.Data.Migrations
{
    public partial class AddedWidgetEventEntity : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "WidgetEvent",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("SqlServer:ValueGenerationStrategy", SqlServerValueGenerationStrategy.IdentityColumn),
                    EventDetails = table.Column<string>(nullable: true),
                    EventLevel = table.Column<string>(nullable: true),
                    EventTitle = table.Column<string>(nullable: true),
                    IsResolved = table.Column<bool>(nullable: false),
                    RecievedUtc = table.Column<DateTime>(nullable: false),
                    SiteId = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_WidgetEvent", x => x.Id);
                    table.ForeignKey(
                        name: "FK_WidgetEvent_Site_SiteId",
                        column: x => x.SiteId,
                        principalTable: "Site",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_WidgetEvent_SiteId",
                table: "WidgetEvent",
                column: "SiteId");
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "WidgetEvent");
        }
    }
}
