import { Injectable } from "@angular/core";
import { Router, CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { Http, Response, RequestOptionsArgs, Headers } from "@angular/http";
import { environment } from '../../../environments/environment';
import { IAuthorizationManager } from "./i-authorization.manager";
import { ILogger } from "./../logging/i-logger";
import { ConsoleLogger } from "./../logging/console/console.logger";
import { TokenResponse, UserIdentityInfo } from './../../services/serverApi/index';
import { Variable } from '../variable';
@Injectable()
export class AuthorizationManager implements IAuthorizationManager, CanActivate {
    private _lastUser: any;
    private _postAuthorizationDefaultUrl: string = '/';

    get lastUser(): any {
        return this._lastUser;
    }
    get user(): Promise<any> {
        return Promise.resolve(this._lastUser);
    }

    get postAuthRedirectUrl(): string {
        let result: string = localStorage.getItem("postAuthRedirectUrl");
        return result ? result : this._postAuthorizationDefaultUrl;
    }
    set postAuthRedirectUrl(value: string) {
        localStorage.setItem("postAuthRedirectUrl", value);
    }

    protected baseUrl: string;
    /// injected dependencies
    protected logger: ILogger;
    protected router: Router;
    protected http: Http;
    /// ctor
    constructor(logger: ConsoleLogger, router: Router, http: Http) {
        this.logger = logger;
        this.router = router;
        this.http = http;
        this.baseUrl = environment.apiUrl
    }
    /// authorization guard implementation
    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
        if (this._lastUser) {
            return true;
        }
        let storedUser = localStorage.getItem('currentUser');
        if (storedUser) {
            this._lastUser = JSON.parse(storedUser);
            return true;
        }
        this.router.navigate(['/login'], { queryParams: { returnUrl: state.url } });
        return false;
    }
    /// methods
    signInViaUsername(login: string, password: string, isPersist: boolean): Promise<any> {
        let self = this;
        let body: any = {
            'login': login,
            'password': password,
            'grantType': 'username'
        };
        return new Promise((resolve: any, reject: any) => {
            return self.http
                .post(`${self.baseUrl}/token`, body)
                .subscribe(
                    (res: any) => {
                        resolve(self.handleResponse(res));
                    },
                    (error: any) => {
                        return reject(error);
                    })
            })
            .then(function (response: TokenResponse) {
                self._lastUser = response;
                return new Promise((resolve: any, reject: any) => {
                    self.user
                        .then(function (user: any): any {
                            return self.http
                                .get(`${self.baseUrl}/user-identity-info`, self.extendOptionsWithHeaders(user, null))
                                .subscribe(
                                    (res: any) => {
                                        resolve(self.handleResponse(res));
                                    },
                                    (error: any) => {
                                        //TODO: resolve this
                                        return reject(error);
                                    });
                        })
                    })
                    .then(function (info: UserIdentityInfo) {
                        self._lastUser.id = info.userId;
                        self._lastUser.avatarUrl = info.avatarUrl;
                        self._lastUser.avatarUrl = info.grantedRoles;
                        self._lastUser.avatarUrl = info.grantedPermissions;
                        if (isPersist) {
                            localStorage.setItem('currentUser', JSON.stringify(self._lastUser));
                        }
                    });
            });
    }
    signOut(): Promise<any> {
        localStorage.removeItem('currentUser');
        this._lastUser = null;
        return Promise.resolve();
    }
    handleAuthorizationCallback(): Promise<void> {
        this.logger.logDebug("HandleAuthorizationCallback method (in AuthorizationManager) called.");
        return Promise.resolve();
    }
    /// helpers
    private extendOptionsWithHeaders(user: any, options?: RequestOptionsArgs): RequestOptionsArgs {
        let opt: any = !options ? {} : options;
        opt.headers = !opt.headers ? new Headers() : opt.headers;
        this.createAuthorizationHeader(opt.headers, user);
        this.createCorsHeader(opt.headers);
        return opt;
    }
    private createAuthorizationHeader(headers: Headers, user: any): void {
        if (Variable.isNotNullOrUndefined(user)) {
            headers.append('Authorization', `Bearer ${user.token}`);
        }
    }
    private createCorsHeader(headers: Headers): void {
        headers.append('Access-Control-Allow-Origin', '*');
    }
    private handleResponse(response: Response): any {
        try {
            return response.json();
        } catch (e) {
            return response.text();
        }
    }
}