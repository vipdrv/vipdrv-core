import { Injectable } from "@angular/core";
import { IAuthorizationManager } from "./i-authorization.manager";
import { ILogger } from "./../logging/i-logger";
import { ConsoleLogger } from "./../logging/console/console.logger";

@Injectable()
export class AuthorizationManager implements IAuthorizationManager {
    /// inited with stub used and have to be changed after authorization development
    private _lastUser: any = { id: 1 };
    private _postAuthorizationDefaultUrl: string = "/";

    get lastUser(): any {
        return this._lastUser;
    }
    get user(): Promise<any> {
        return Promise.resolve(null);
    }

    get postAuthRedirectUrl(): string {
        let result: string = localStorage.getItem("postAuthRedirectUrl");
        return result ? result : this._postAuthorizationDefaultUrl;
    }
    set postAuthRedirectUrl(value: string) {
        localStorage.setItem("postAuthRedirectUrl", value);
    }

    /// injected dependencies
    protected logger: ILogger;

    /// ctor
    constructor(logger: ConsoleLogger) {
        this.logger = logger;
    }

    /// methods
    signIn(args?: any): Promise<any> {
        this.logger.logDebug("SignIn method (in AuthorizationManager) called.");
        return Promise.resolve();
    }
    signOut(args?: any): Promise<any> {
        this.logger.logDebug("SignOut method (in AuthorizationManager) called.");
        return Promise.resolve();
    }
    handleAuthorizationCallback(): Promise<void> {
        this.logger.logDebug("HandleAuthorizationCallback method (in AuthorizationManager) called.");
        return Promise.resolve();
    }
}