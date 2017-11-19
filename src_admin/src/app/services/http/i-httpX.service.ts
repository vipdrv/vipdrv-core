import { RequestOptionsArgs } from '@angular/http';
export interface IHttpXService {
    get(url: string, options?: RequestOptionsArgs): Promise<any>;
    post(url: string, body: any, options?: RequestOptionsArgs): Promise<any>;
    put(url: string, body: any, options?: RequestOptionsArgs): Promise<any>;
    delete(url: string, options?: RequestOptionsArgs): Promise<any>;
    patch(url: string, body: any, options?: RequestOptionsArgs): Promise<any>;
    isUnauthorizedError(reason: any): boolean;
}