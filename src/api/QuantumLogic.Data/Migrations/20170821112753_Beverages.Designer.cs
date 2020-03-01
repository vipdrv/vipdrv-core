using System;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Infrastructure;
using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Migrations
{
    [DbContext(typeof(QuantumLogicDbContext))]
    [Migration("20170821112753_Beverages")]
    partial class Beverages
    {
        protected override void BuildTargetModel(ModelBuilder modelBuilder)
        {
            modelBuilder
                .HasAnnotation("ProductVersion", "1.1.2")
                .HasAnnotation("SqlServer:ValueGenerationStrategy", SqlServerValueGenerationStrategy.IdentityColumn);

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.MainModule.User", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("Email");

                    b.Property<int>("MaxSitesCount");

                    b.Property<string>("Password");

                    b.HasKey("Id");

                    b.ToTable("User");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Beverage", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("Description");

                    b.Property<bool>("IsActive");

                    b.Property<string>("Name");

                    b.Property<int>("Order");

                    b.Property<string>("PhotoUrl");

                    b.Property<int>("SiteId");

                    b.HasKey("Id");

                    b.HasIndex("SiteId");

                    b.ToTable("Beverage");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Expert", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("Description");

                    b.Property<string>("FacebookUrl");

                    b.Property<bool>("IsActive");

                    b.Property<string>("LinkedinUrl");

                    b.Property<string>("Name");

                    b.Property<int>("Order");

                    b.Property<string>("PhotoUrl");

                    b.Property<int>("SiteId");

                    b.Property<string>("WorkingHours");

                    b.HasKey("Id");

                    b.HasIndex("SiteId");

                    b.ToTable("Experts");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Site", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd();

                    b.Property<string>("BeautyId");

                    b.Property<string>("Contacts");

                    b.Property<string>("Name");

                    b.Property<string>("Url");

                    b.Property<int>("UserId");

                    b.HasKey("Id");

                    b.ToTable("Site");
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Beverage", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Site", "Site")
                        .WithMany("Beverages")
                        .HasForeignKey("SiteId")
                        .OnDelete(DeleteBehavior.Cascade);
                });

            modelBuilder.Entity("QuantumLogic.Core.Domain.Entities.WidgetModule.Expert", b =>
                {
                    b.HasOne("QuantumLogic.Core.Domain.Entities.WidgetModule.Site", "Site")
                        .WithMany()
                        .HasForeignKey("SiteId")
                        .OnDelete(DeleteBehavior.Cascade);
                });
        }
    }
}
