namespace QuantumLogic.WebApi.Constants
{
    /// <summary>
    /// Contains the values of status codes defined for HTTP (custom MCSCore extensions).
    /// </summary>
    public enum CustomHttpCode
    {
        /// <summary>
        /// The request was well-formed but was unable to be followed due to semantic errors.
        /// </summary>
        UnprocessableEntity = 422,
        /// <summary>
        /// The request's Http header is not valid.
        /// </summary>
        InvalidHeader = 432,
    }
}
