import { Injectable } from '@angular/core';
import { HttpService, ConsoleLogger } from './../../../../utils/index';
import { SiteEntity, LightEntity } from './../../../../entities/index';
import { ISiteApiService } from './i-site.api-service';
import { CRUDApiService } from './../../crud.api-service';
@Injectable()
export class SiteApiService extends CRUDApiService<SiteEntity, number, LightEntity> implements ISiteApiService {
    /// ctor
    constructor(
        httpService: HttpService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'site');
    }
    /// methods

    /// helpers
    protected createEmptyEntity(): SiteEntity {
        return new SiteEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}
