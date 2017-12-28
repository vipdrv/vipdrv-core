namespace QuantumLogic.Core.Utils.Email.Data
{
    public interface IEmailTemplate
    {
        /// <summary>
        /// Is used to create HTML email template
        /// </summary>
        /// <returns>
        /// string with HTML
        /// </returns>
        string AsHtml();

        /// <summary>
        /// Is used to get HTML email template as plain text
        /// </summary>
        /// <returns>
        /// string with plain text
        /// </returns>
        string AsPlainText();
    }
}
