import { Injectable } from '@angular/core';
import { Router, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { Http, Response, RequestOptionsArgs, Headers } from '@angular/http';
import { environment } from '../../../environments/environment';
import { ILogger, ConsoleLogger, Variable } from './../../utils/index';
import { TokenResponse, UserIdentityInfo } from './../index';
import { IAuthorizationService } from './i-authorization.service';
@Injectable()
export class AuthorizationService implements IAuthorizationService {
    private _lastUser: any;
    private _postAuthorizationDefaultUrl: string = '/';

    get lastUser(): any {
        //TODO: fix this after resolve singleton problem (should return just last user) - this service is singleton
        //return this._lastUser;
        return this.getStoredUser();
    }
    get user(): Promise<any> {
        return Promise.resolve(this.getStoredUser());
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
        this.baseUrl = environment.apiUrl;
        this._lastUser = this.getStoredUser();
    }
    protected getStoredUser(): any {
        let storedUser;
        if (sessionStorage.getItem('currentUser')) {
            storedUser = JSON.parse(sessionStorage.getItem('currentUser'));
        } else if (localStorage.getItem('currentUser')) {
            storedUser = JSON.parse(localStorage.getItem('currentUser'));
        } else {
            storedUser = null;
        }
        return storedUser;
    }
    /// authorization guard implementation
    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
        if (this._lastUser) {
            return true;
        }
        let storedUser = this.getStoredUser();
        if (storedUser) {
            this._lastUser = storedUser;
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
                if (isPersist) {
                    localStorage.setItem('currentUser', JSON.stringify(self._lastUser));
                } else {
                    sessionStorage.setItem('currentUser', JSON.stringify(self._lastUser));
                }
            });
    }
    signOut(): Promise<any> {
        localStorage.removeItem('currentUser');
        sessionStorage.removeItem('currentUser');
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