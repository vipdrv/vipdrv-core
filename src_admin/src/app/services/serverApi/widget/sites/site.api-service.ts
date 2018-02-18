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
    swapBeverageExpertStepOrder(siteId: number): Promise<void> {
        const self = this;
        const methodName: string = 'swap-step-order-beverage-expert';
        return self.httpService
            .patch(self.createUrlWithMethodNameAndParams(methodName, String(siteId)), {})
            .then(function (response: any): void { });
    }
    swapBeverageRouteStepOrder(siteId: number): Promise<void> {
        const self = this;
        const methodName: string = 'swap-step-order-beverage-route';
        return self.httpService
            .patch(self.createUrlWithMethodNameAndParams(methodName, String(siteId)), {})
            .then(function (response: any): void { });
    }
    swapExpertRouteStepOrder(siteId: number): Promise<void> {
        const self = this;
        const methodName: string = 'swap-step-order-expert-route';
        return self.httpService
            .patch(self.createUrlWithMethodNameAndParams(methodName, String(siteId)), {})
            .then(function (response: any): void { });
    }

    patchContacts(id: number, value: any): Promise<void> {
        const self = this;
        const methodName: string = 'change-contacts';
        return self.httpService
            .patch(self.createUrlWithMethodNameAndParams(methodName, String(id)), { 'value': value })
            .then(function (response: any): void { });
    }
    patchUseExpertStep(id: number, value: boolean): Promise<void> {
        const self = this;
        const methodName: string = 'change-use-expert-step';
        return self.httpService
            .patch(self.createUrlWithMethodNameAndParams(methodName, String(id)), { 'value': value })
            .then(function (response: any): void { });
    }
    patchUseBeverageStep(id: number, value: boolean): Promise<void> {
        const self = this;
        const methodName: string = 'change-use-beverage-step';
        return self.httpService
            .patch(self.createUrlWithMethodNameAndParams(methodName, String(id)), { 'value': value })
            .then(function (response: any): void { });
    }
    patchUseRouteStep(id: number, value: boolean): Promise<void> {
        const self = this;
        const methodName: string = 'change-use-route-step';
        return self.httpService
            .patch(self.createUrlWithMethodNameAndParams(methodName, String(id)), { 'value': value })
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
