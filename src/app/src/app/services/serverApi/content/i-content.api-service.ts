import { IApiService } from './../i-api-service';
/// is used to communicate with server's sites controller
export interface IContentApiService extends IApiService {
    postImage(image: any): Promise<string>;
}