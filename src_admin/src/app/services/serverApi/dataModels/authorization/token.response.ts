export class TokenResponse {
    issuer: string;
    audiences: Array<string>;
    token: string;
    sid: string;
    username: string;
    expireDateTimeUtc: string;
}