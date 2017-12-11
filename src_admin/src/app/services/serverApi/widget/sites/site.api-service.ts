import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../utils/index';
import { HttpXService } from './../../../index';
import { SiteEntity, LightEntity } from './../../../../entities/index';
import { ISiteApiService } from './i-site.api-service';
import { CRUDApiService } from './../../crud.api-service';
@Injectable()
export class SiteApiService extends CRUDApiService<SiteEntity, number, LightEntity> implements ISiteApiService {
    /// ctor
    constructor(
        httpService: HttpXService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'site');
        this.logger.logDebug('SiteApiService: Service has been constructed.');
    }
    /// methods
    patchContacts(id: number, value: any): Promise<void> {
        const self = this;
        const methodName: string = 'change-contacts';
        return this.httpService
            .patch(this.createUrlWithMethodNameAndParams(methodName, String(id)), { 'value': value })
            .then(function (response: any): void { });
    }
    /// helpers
    protected createEmptyEntity(): SiteEntity {
        return new SiteEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}
