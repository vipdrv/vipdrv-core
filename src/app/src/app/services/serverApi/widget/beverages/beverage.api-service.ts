import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../utils/index';
import { HttpXService } from './../../../index';
import { BeverageEntity, LightEntity } from './../../../../entities/index';
import { CRUDXApiService } from './../../crudx.api-service';
import { IBeverageApiService } from './i-beverage.api-service';
@Injectable()
export class BeverageApiService
    extends CRUDXApiService<BeverageEntity, number, LightEntity>
    implements IBeverageApiService {
    /// ctor
    constructor(
        httpService: HttpXService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'beverage');
    }
    /// methods

    /// helpers
    protected createEmptyEntity(): BeverageEntity {
        return new BeverageEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}
