import { Injectable } from '@angular/core';
import { HttpService, ConsoleLogger } from './../../../../utils/index';
import { ExpertEntity, LightEntity } from './../../../../entities/index';
import { CRUDXApiService } from './../../crudx.api-service';
import { IExpertApiService } from './i-expert.api-service';
@Injectable()
export class ExpertApiService extends CRUDXApiService<ExpertEntity, number, LightEntity> implements IExpertApiService {
    /// ctor
    constructor(
        httpService: HttpService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'expert');
    }
    /// methods

    /// helpers
    protected createEmptyEntity(): ExpertEntity {
        return new ExpertEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}

