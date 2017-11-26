import { Injectable } from '@angular/core';
import { Http, Response, RequestOptionsArgs, Headers } from '@angular/http';
import { IHttpXService } from './i-httpX.service';
import { IAuthorizationService, AuthorizationService } from './../index';
import { ConsoleLogger, ILogger } from './../../utils/index';
@Injectable()
export class HttpXService implements IHttpXService {
    /// injected dependencies
    protected http: Http;
    protected logger: ILogger;
    protected authorizationService: IAuthorizationService;
    /// ctor
    constructor(
        http: Http,
        logger: ConsoleLogger,
        authorizationService: AuthorizationService) {
        this.http = http;
        this.logger = logger;
        this.authorizationService = authorizationService;
        this.logger.logDebug('HttpXService: Service has been constructed.');
    }
    /// methods
    get(url: string, options?: RequestOptionsArgs): Promise<any> {
        const self: HttpXService = this;
        return new Promise((resolve: any, reject: any) => {
            return self.http
                .get(url, self.extendOptionsWithHeaders(options))
                .subscribe(
                    (res: any) => {
                        resolve(self.handleResponse(res));
                    },
                    (error: any) => {
                        if (self.authorizationService.isUnauthorizedError(error)) {
                            return self.authorizationService.handleUnauthorizedResponse();
                        } else {
                            return reject(error);
                        }
                    });
        });
    }
    post(url: string, body: any, options?: RequestOptionsArgs): Promise<any> {
        const self: HttpXService = this;
        return new Promise((resolve: any, reject: any) => {
            return self.http
                .post(url, body, self.extendOptionsWithHeaders(options))
                .subscribe(
                    (res: any) => {
                        resolve(self.handleResponse(res));
                    },
                    (error: any) => {
                        if (self.authorizationService.isUnauthorizedError(error)) {
                            return self.authorizationService.handleUnauthorizedResponse();
                        } else {
                            return reject(error);
                        }
                    });
        });
    }
    put(url: string, body: any, options?: RequestOptionsArgs): Promise<any> {
        const self: HttpXService = this;
        return new Promise((resolve: any, reject: any) => {
            return self.http
                .put(url, body, self.extendOptionsWithHeaders(options))
                .subscribe(
                    (res: any) => {
                        resolve(self.handleResponse(res));
                    },
                    (error: any) => {
                        if (self.authorizationService.isUnauthorizedError(error)) {
                            return self.authorizationService.handleUnauthorizedResponse();
                        } else {
                            return reject(error);
                        }
                    });
        });
    }
    delete(url: string, options?: RequestOptionsArgs): Promise<any> {
        const self: HttpXService = this;
        return new Promise((resolve: any, reject: any) => {
            return self.http
                .delete(url, self.extendOptionsWithHeaders(options))
                .subscribe(
                    (res: any) => {
                        resolve(self.handleResponse(res));
                    },
                    (error: any) => {
                        if (self.authorizationService.isUnauthorizedError(error)) {
                            return self.authorizationService.handleUnauthorizedResponse();
                        } else {
                            return reject(error);
                        }
                    });
        });
    }
    patch(url: string, body: any, options?: RequestOptionsArgs): Promise<any> {
        const self: HttpXService = this;
        return new Promise((resolve: any, reject: any) => {
            return self.http
                .patch(url, body, self.extendOptionsWithHeaders(options))
                .subscribe(
                    (res: any) => {
                        resolve(self.handleResponse(res));
                    },
                    (error: any) => {
                        if (self.authorizationService.isUnauthorizedError(error)) {
                            return self.authorizationService.handleUnauthorizedResponse();
                        } else {
                            return reject(error);
                        }
                    });
        });
    }
    /// helpers
    private extendOptionsWithHeaders(user: any, options?: RequestOptionsArgs): RequestOptionsArgs {
        const opt: any = options ? options : {};
        opt.headers = opt.headers ? opt.headers : new Headers();
        this.createAuthorizationHeader(opt.headers, user);
        this.createCorsHeader(opt.headers);
        return opt;
    }
    private createAuthorizationHeader(headers: Headers, user: any): void {
        headers.append('Authorization', this.authorizationService.getAuthorizationToken());
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