using System;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Infrastructure;
using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;
using QuantumLogic.Data.EFContext;

namespace QuantumLogic.Data.Migrations
{
    [DbContext(typeof(QuantumLogicDbContext))]
    [Migration("20170819134429_InitialCreate")]
    partial class InitialCreate
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
        }
    }
}
