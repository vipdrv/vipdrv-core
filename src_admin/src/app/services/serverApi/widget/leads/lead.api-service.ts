import { Injectable } from '@angular/core';
import { HttpService, ConsoleLogger } from './../../../../utils/index';
import { LeadEntity, LightEntity } from './../../../../entities/index';
import { CRUDApiService } from './../../crud.api-service';
import { ILeadApiService } from './i-lead.api-service';
@Injectable()
export class LeadApiService extends CRUDApiService<LeadEntity, number, LightEntity> implements ILeadApiService {
    /// ctor
    constructor(
        httpService: HttpService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'lead');
    }
    /// methods

    /// helpers
    protected createEmptyEntity(): LeadEntity {
        return new LeadEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}
