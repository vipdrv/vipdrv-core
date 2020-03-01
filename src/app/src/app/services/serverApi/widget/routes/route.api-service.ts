import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../utils/index';
import { HttpXService } from './../../../index';
import { RouteEntity, LightEntity } from './../../../../entities/index';
import { CRUDXApiService } from './../../crudx.api-service';
import { IRouteApiService } from './i-route.api-service';
@Injectable()
export class RouteApiService extends CRUDXApiService<RouteEntity, number, LightEntity> implements IRouteApiService {
    /// ctor
    constructor(
        httpService: HttpXService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'route');
    }
    /// methods

    /// helpers
    protected createEmptyEntity(): RouteEntity {
        return new RouteEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}
