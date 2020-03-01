import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../utils/index';
import { HttpXService } from './../../../index';
import { StepEntity, LightEntity } from './../../../../entities/index';
import { CRUDXApiService } from './../../crudx.api-service';
import { IStepApiService } from './i-Step.api-service';
@Injectable()
export class StepApiService
    extends CRUDXApiService<StepEntity, number, LightEntity>
    implements IStepApiService {
    /// ctor
    constructor(
        httpService: HttpXService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'step');
    }
    /// methods

    /// helpers
    protected createEmptyEntity(): StepEntity {
        return new StepEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}
