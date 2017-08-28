using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.Extensions.Options;
using QuantumLogic.Core.Domain.Entities.MainModule;
using QuantumLogic.Core.Domain.Entities.WidgetModule;
using QuantumLogic.Data.Configurations;

namespace QuantumLogic.Data.EFContext
{
    /// <summary>
    /// Is used as application's main data base context
    /// </summary>
    public class QuantumLogicDbContext : DbContext
    {
        #region Injected dependencies

        protected DataConfiguration DataConfiguration { get; set; }

        #endregion

        #region Ctors

        public QuantumLogicDbContext(IOptions<DataConfiguration> dataConfiguration)
            : base()
        {
            DataConfiguration = dataConfiguration.Value;
        }

        #endregion

        #region DbSets

        public virtual DbSet<User> Users { get; set; }
        public virtual DbSet<Site> Sites { get; set; }
        public virtual DbSet<Beverage> Beverages { get; set; }
        public virtual DbSet<Expert> Experts { get; set; }
        public virtual DbSet<Route> Routes { get; set; }
        public virtual DbSet<WidgetTheme> WidgetThemes { get; set; }
        public virtual DbSet<Lead> Leads { get; set; }

        #endregion

        public QuantumLogicDbContext()
            : base()
        { }

        protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
        {
            // optionsBuilder.UseSqlServer(DataConfiguration.DefaultConnection.ConnectionString);
            
            // TODO: hardcoded connection string
            optionsBuilder.UseSqlServer("Server=mysql.dealer-advance.com;Database=dev_quantumlogic;User Id=sa-quantumlogic-2; Password=2YAfUFq9ZFsnLAgA;");
            //Database.SetCommandTimeout(DBCommandTimeout);
        }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            modelBuilder.Entity<User>(entity =>
            {
                entity.ToTable("User");
                entity.HasKey(c => c.Id);
            });

            modelBuilder.Entity<Site>(entity =>
            {
                entity.ToTable("Site");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.User)
                    .WithMany(b => b.Sites)
                    .HasForeignKey(r => r.UserId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Beverage>(entity =>
            {
                entity.ToTable("Beverage");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.Site)
                    .WithMany(b => b.Beverages)
                    .HasForeignKey(r => r.SiteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Expert>(entity =>
            {
                entity.ToTable("Expert");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.Site)
                    .WithMany(b => b.Experts)
                    .HasForeignKey(r => r.SiteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Route>(entity =>
            {
                entity.ToTable("Route");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.Site)
                    .WithMany(b => b.Routes)
                    .HasForeignKey(r => r.SiteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<WidgetTheme>(entity =>
            {
                entity.ToTable("WidgetTheme");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(p => p.Site)
                    .WithOne(i => i.WidgetTheme)
                    .HasForeignKey<WidgetTheme>(b => b.SiteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            modelBuilder.Entity<Lead>(entity =>
            {
                entity.ToTable("Lead");
                entity.HasKey(c => c.Id);
                entity
                    .HasOne(e => e.Site)
                    .WithMany(b => b.Leads)
                    .HasForeignKey(r => r.SiteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });
        }
    }
}
