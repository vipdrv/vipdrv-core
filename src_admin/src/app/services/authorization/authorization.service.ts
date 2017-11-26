import { Injectable } from '@angular/core';
import { Router, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { Http, Response, RequestOptionsArgs, Headers } from '@angular/http';
import { environment } from '../../../environments/environment';
import { Variable, ILogger, ConsoleLogger } from './../../utils/index';
import { TokenResponse, UserIdentityInfo } from './../index';
import { IAuthorizationService } from './i-authorization.service';
import { UserEntity } from './../../entities/index';
@Injectable()
export class AuthorizationService implements IAuthorizationService {
    /// service fields
    protected postAuthRedirectUrlInStorageKey: string = 'postAuthRedirectUrl';
    protected currentUserInfoInStorageKey: string = 'currentUserInfo';
    private _currentUser: UserEntity;
    private _currentUserId: number;
    private _currentUserInfo: any;
    private _postAuthorizationDefaultUrl: string = '/';
    protected baseUrl: string;
    /// public properties
    get currentUserId(): number {
        return Variable.isNotNullOrUndefined(this._currentUserId) ? this._currentUserId : null;
    }
    get currentUser(): UserEntity {
        return Variable.isNotNullOrUndefined(this._currentUser) ? this._currentUser : null;
    }
    get postAuthRedirectUrl(): string {
        const result: string = localStorage.getItem(this.postAuthRedirectUrlInStorageKey);
        return result ? result : this._postAuthorizationDefaultUrl;
    }
    set postAuthRedirectUrl(value: string) {
        localStorage.setItem(this.postAuthRedirectUrlInStorageKey, value);
    }
    /// injected dependencies
    protected logger: ILogger;
    protected router: Router;
    protected http: Http;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        router: Router,
        http: Http) {
        this.logger = logger;
        this.router = router;
        this.http = http;
        this.baseUrl = environment.apiUrl;
        this.logger.logDebug('AuthorizationService: Service has been constructed.');
        this.initializeService();
    }
    /// methods
    initializeService(): void {
        this.applyStoredUserInfo();
        if (Variable.isNotNullOrUndefined(this.currentUserId)) {
            this.actualizeCurrentUserProfile();
        }
    }
    /// authorization guard implementation
    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
        if (Variable.isNotNullOrUndefined(this.currentUserId)) {
            return true;
        }
        this.applyStoredUserInfo();
        if (Variable.isNotNullOrUndefined(this.currentUserId)) {
            return true;
        }
        this.router.navigate(['/login'], { queryParams: { returnUrl: state.url } });
        return false;
    }
    /// methods
    getAuthorizationToken(): string {
        if (Variable.isNotNullOrUndefined(this._currentUserInfo)) {
            return `${this._currentUserInfo.tokenType} ${this._currentUserInfo.token}`;
        } else {
            return null;
        }
    }
    signInViaUsername(login: string, password: string, isPersist: boolean): Promise<any> {
        const self = this;
        const body: any = {
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
                        });
            })
            .then(function (response: TokenResponse): Promise<void> {
                if (isPersist) {
                    localStorage.setItem(self.currentUserInfoInStorageKey, JSON.stringify(response));
                } else {
                    sessionStorage.setItem(self.currentUserInfoInStorageKey, JSON.stringify(response));
                }
                self.applyStoredUserInfo();
                return self.actualizeCurrentUserProfile();
            });
    }
    actualizeCurrentUserProfile(): Promise<void> {
        const self = this;
        return new Promise((resolve: any, reject: any) => {
            return self.http
                .get(`${self.baseUrl}/api/user/${self.currentUserId}`, self.extendOptionsWithHeaders())
                .subscribe(
                    (res: any) => {
                        resolve(self.handleResponse(res));
                    },
                    (error: any) => {
                        return reject(error);
                    });
            })
            .then(function (response: UserEntity): void {
                const entity: UserEntity = new UserEntity();
                entity.initializeFromDto(response);
                self._currentUser = entity;
                self.logger.logTrase('AuthorizationService: Current user profile has been actualized.');
            });
    }
    signOut(): Promise<any> {
        localStorage.removeItem(this.currentUserInfoInStorageKey);
        sessionStorage.removeItem(this.currentUserInfoInStorageKey);
        this._currentUser = null;
        this._currentUserId = null;
        return Promise.resolve();
    }
    handleUnauthorizedResponse(): Promise<void> {
        this.logger.logWarning('AuthorizationService: handleUnauthorizedResponse method called.');
        return this.router
            .navigate(['/logout'], { queryParams: { returnUrl: this.router.url } })
            .then(function (result: boolean): void {

            });
    }
    isUnauthorizedError(reason: any): boolean {
        return !!reason && reason.status === 401;
    }
    /// helpers
    private applyStoredUserInfo(): void {
        if (sessionStorage.getItem(this.currentUserInfoInStorageKey)) {
            this._currentUserInfo = JSON.parse(sessionStorage.getItem(this.currentUserInfoInStorageKey));
        } else if (localStorage.getItem(this.currentUserInfoInStorageKey)) {
            this._currentUserInfo = JSON.parse(localStorage.getItem(this.currentUserInfoInStorageKey));
        } else {
            this._currentUserInfo = null;
        }
        if (Variable.isNotNullOrUndefined(this._currentUserInfo)) {
            this._currentUserId = this._currentUserInfo.userId;
        }
    }
    private extendOptionsWithHeaders(options?: RequestOptionsArgs): RequestOptionsArgs {
        const opt: any = !options ? {} : options;
        opt.headers = !opt.headers ? new Headers() : opt.headers;
        this.createAuthorizationHeader(opt.headers);
        this.createCorsHeader(opt.headers);
        return opt;
    }
    private createAuthorizationHeader(headers: Headers): void {
        headers.append('Authorization', this.getAuthorizationToken());
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