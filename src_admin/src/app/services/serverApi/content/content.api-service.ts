import { Injectable } from '@angular/core';
import { ConsoleLogger, Variable } from './../../../utils/index';
import { HttpXService } from './../../index';

import { BaseApiService } from './../base.api-service';
import { IContentApiService } from './i-content.api-service';
@Injectable()
export class ContentApiService extends BaseApiService implements IContentApiService {
    /// ctor
    constructor(httpService: HttpXService, logger: ConsoleLogger) {
        super(httpService, logger, 'content');
    }
    /// methods
    postImage(image: any): Promise<string> {
        return this.httpService
            .post(this.createUrlWithMethodName('image'), { 'img': String(image) })
            .then(function (imageUrl: string): string {
                return imageUrl;
            });
    }
}