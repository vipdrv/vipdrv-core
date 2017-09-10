import { Injectable } from '@angular/core';
import { IHttpService, ILogger,  } from './../../utils/index';
import { IEntity, ILightEntity } from './../../entities/index';
import { ICRUDXApiService } from './i-crudx.api-service';
import { CRUDApiService } from './crud.api-service';
import { UrlParameter } from './urlParameter';
@Injectable()
export abstract class CRUDXApiService<TEntity extends IEntity<TKey>, TKey, TLightEntity extends ILightEntity<TKey>>
    extends CRUDApiService<TEntity, TKey, TLightEntity>
    implements ICRUDXApiService<TEntity, TKey, TLightEntity> {
    /// ctor
    constructor(
        httpService: IHttpService,
        logger: ILogger,
        controllerName: string) {
        super(httpService, logger, controllerName);
    }
    patchActivity(id: TKey, value: boolean): Promise<void> {
        let self = this;
        let methodName: string = 'change-activity';
        return this.httpService
            .patch(this.createUrlWithMethodNameAndParams(methodName, String(id)), { 'value': value })
            .then(function (response: any): void { });
    }
    patchOrder(id: TKey, value: number): Promise<void> {
        let self = this;
        let methodName: string = 'change-order';
        return this.httpService
            .patch(this.createUrlWithMethodNameAndParams(methodName, String(id)), { 'value': value })
            .then(function (response: any): void { });
    }
}
