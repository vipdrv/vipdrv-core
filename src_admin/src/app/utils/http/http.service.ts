import { Injectable } from "@angular/core";
import { Http, Response, RequestOptionsArgs, Headers } from "@angular/http";
import { IHttpService } from "./i-http.service";
import { IAuthorizationManager } from "./../auth/i-authorization.manager";
import { AuthorizationManager } from "./../auth/authorization.manager";
import { ILogger } from "./../logging/i-logger";
import { ConsoleLogger } from "./../logging/console/console.logger";
@Injectable()
export class HttpService implements IHttpService {
    /// injected dependencies
    protected http: Http;
    protected logger: ILogger;
    protected authorizationManager: IAuthorizationManager;
    /// ctor
    constructor(
        http: Http,
        logger: ConsoleLogger,
        authorizationManager: AuthorizationManager) {
        this.http = http;
        this.logger = logger;
        this.authorizationManager = authorizationManager;
    }
    /// methods
    get(url: string, options?: RequestOptionsArgs, supressRecursion: boolean = false): Promise<any> {
        let self: HttpService = this;
        return new Promise((resolve: any, reject: any) => {
            self.authorizationManager.user
                .then(function (user: any): any {
                    return self.http
                        .get(url, self.extendOptionsWithHeaders(user, options))
                        .subscribe(
                        (res: any) => {
                            resolve(self.handleResponse(res));
                        },
                        (error: any) => {
                            if (self.isUnauthorizedError(error)) {
                                return self.authorizationManager.user
                                    .then(function (r: any): Promise<any> {
                                        if (!!r && !r.expired && !supressRecursion) {
                                            return self.get(url, options, true);
                                        } else {
                                            return self.authorizationManager.signIn();
                                        }
                                    });
                            } else {
                                return reject(error);
                            }
                        });
                })
                .catch((reason: any) => {
                    self.handleCriticalError("get", reason);
                });
        });
    }
    post(url: string, body: any, options?: RequestOptionsArgs, supressRecursion: boolean = false): Promise<any> {
        let self: HttpService = this;
        return new Promise((resolve: any, reject: any) => {
            self.authorizationManager.user
                .then(function (user: any): any {
                    return self.http
                        .post(url, body, self.extendOptionsWithHeaders(user, options))
                        .subscribe(
                        (res: any) => {
                            resolve(self.handleResponse(res));
                        },
                        (error: any) => {
                            if (self.isUnauthorizedError(error)) {
                                return self.authorizationManager.user
                                    .then(function (r: any): Promise<any> {
                                        if (!!r && !r.expired && !supressRecursion) {
                                            return self.post(url, body, options, true);
                                        } else {
                                            return self.authorizationManager.signIn();
                                        }
                                    });
                            } else {
                                return reject(error);
                            }
                        });
                })
                .catch((reason: any) => {
                    self.handleCriticalError("post", reason);
                });
        });
    }
    put(url: string, body: any, options?: RequestOptionsArgs, supressRecursion: boolean = false): Promise<any> {
        let self: HttpService = this;
        return new Promise((resolve: any, reject: any) => {
            self.authorizationManager.user
                .then(function (user: any): any {
                    return self.http
                        .put(url, body, self.extendOptionsWithHeaders(user, options))
                        .subscribe(
                            (res: any) => {
                                resolve(self.handleResponse(res));
                            },
                            (error: any) => {
                                if (self.isUnauthorizedError(error)) {
                                    return self.authorizationManager.user
                                        .then(function (r: any): Promise<any> {
                                            if (!!r && !r.expired && !supressRecursion) {
                                                return self.put(url, body, options, true);
                                            } else {
                                                return self.authorizationManager.signIn();
                                            }
                                        });
                                } else {
                                    return reject(error);
                                }
                            });
                })
                .catch((reason: any) => {
                    self.handleCriticalError("put", reason);
                });
        });
    }
    delete(url: string, options?: RequestOptionsArgs, supressRecursion: boolean = false): Promise<any> {
        let self: HttpService = this;
        return new Promise((resolve: any, reject: any) => {
            self.authorizationManager.user
                .then(function (user: any): any {
                    return self.http
                        .delete(url, self.extendOptionsWithHeaders(user, options))
                        .subscribe(
                            (res: any) => {
                                resolve(self.handleResponse(res));
                            },
                            (error: any) => {
                                if (self.isUnauthorizedError(error)) {
                                    return self.authorizationManager.user
                                        .then(function (r: any): Promise<any> {
                                            if (!!r && !r.expired && !supressRecursion) {
                                                return self.delete(url, options, true);
                                            } else {
                                                return self.authorizationManager.signIn();
                                            }
                                        });
                                } else {
                                    return reject(error);
                                }
                            });
                })
                .catch((reason: any) => {
                    self.handleCriticalError("delete", reason);
                });
        });
    }
    patch(url: string, body: any, options?: RequestOptionsArgs, supressRecursion: boolean = false): Promise<any> {
        let self: HttpService = this;
        return new Promise((resolve: any, reject: any) => {
            self.authorizationManager.user
                .then(function (user: any): any {
                    return self.http
                        .patch(url, body, self.extendOptionsWithHeaders(user, options))
                        .subscribe(
                            (res: any) => {
                                resolve(self.handleResponse(res));
                            },
                            (error: any) => {
                                if (self.isUnauthorizedError(error)) {
                                    return self.authorizationManager.user
                                        .then(function (r: any): Promise<any> {
                                            if (!!r && !r.expired && !supressRecursion) {
                                                return self.patch(url, body, options, true);
                                            } else {
                                                return self.authorizationManager.signIn();
                                            }
                                        });
                                } else {
                                    return reject(error);
                                }
                            });
                })
                .catch((reason: any) => {
                    self.handleCriticalError('patch', reason);
                });
        });
    }
    /// predicates
    isUnauthorizedError(reason: any): boolean {
        return !!reason && reason.status === 401;
    }
    /// helpers
    private handleCriticalError(methodName: string, reason: any): void {
        this.logger.logCritical("Auth critical error on http.service." + methodName + ": " + reason);
        throw new Error(reason);
    }
    private extendOptionsWithHeaders(user: any, options?: RequestOptionsArgs): RequestOptionsArgs {
        let opt: any = !!options ? options : {};
        opt.headers = !!opt.headers ? opt.headers : new Headers();
        this.createAuthorizationHeader(opt.headers, user);
        this.createCorsHeader(opt.headers);
        return opt;
    }
    private createAuthorizationHeader(headers: Headers, user: any): void {
        if (user) {
            headers.append("Authorization", "Bearer " + user.access_token);
        }
    }
    private createCorsHeader(headers: Headers): void {
        headers.append("Access-Control-Allow-Origin", "*");
    }
    private handleResponse(response: Response): any {
        try {
            return response.json();
        } catch (e) {
            return response.text();
        }
    }
}