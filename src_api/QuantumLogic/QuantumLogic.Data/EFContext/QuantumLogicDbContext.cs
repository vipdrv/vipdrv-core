using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Options;
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

        //public virtual DbSet<Entity> Entities { get; set; }

        #endregion

        protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
        {
            optionsBuilder.UseSqlServer(DataConfiguration.DefaultConnection.ConnectionString);
        }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            //modelBuilder.Entity<Entity>(entity =>
            //{
            //    entity.ToTable("table_name");
            //    entity.Property(r => r.Id).HasColumnName("id");
            //    entity
            //        .HasOne(e => e.OtherEntity)
            //        .WithMany()
            //        .HasForeignKey(r => r.EntityId)
            //        .IsRequired(false);
            //});
        }
    }
}
