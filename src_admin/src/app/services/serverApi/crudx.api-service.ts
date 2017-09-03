import { Injectable } from '@angular/core';
import { IHttpService, ILogger } from './../../utils/index';
import { IEntity, ILightEntity } from './../../entities/index';
import { ICRUDXApiService } from './i-crudx.api-service';
import { CRUDApiService } from './crud.api-service';
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

}
