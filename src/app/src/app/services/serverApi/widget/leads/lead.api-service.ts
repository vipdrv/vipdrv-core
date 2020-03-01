import { Injectable } from '@angular/core';
import { ConsoleLogger, Variable } from './../../../../utils/index';
import { HttpXService } from './../../../index';
import { LeadEntity, LightEntity } from './../../../../entities/index';
import { CRUDApiService } from './../../crud.api-service';
import { ILeadApiService } from './i-lead.api-service';
import { UrlParameter } from './../../urlParameter';
@Injectable()
export class LeadApiService extends CRUDApiService<LeadEntity, number, LightEntity> implements ILeadApiService {
    /// ctor
    constructor(
        httpService: HttpXService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'lead');
    }
    /// methods
    patchIsNew(id: number, value: boolean): Promise<void> {
        const self = this;
        const methodName: string = 'patch-is-new';
        return self.httpService
            .patch(this.createUrlWithMethodNameAndParams(methodName, String(id)), { 'value': value })
            .then(function (response: any): void { });
    }
    patchIsReachedByManager(id: number, value: boolean): Promise<void> {
        const self = this;
        const methodName: string = 'patch-is-reached-by-manager';
        return self.httpService
            .patch(this.createUrlWithMethodNameAndParams(methodName, String(id)), { 'value': value })
            .then(function (response: any): void { });
    }
    exportToExcel(page: number, pageSize: number, sorting: string, filter: any): Promise<string> {
        const methodName = 'export/excel';
        const pageParameter = new UrlParameter('page', page);
        const pageSizeParameter = new UrlParameter('pageSize', pageSize);
        const request = Variable.isNotNullOrUndefined(filter) ? filter : {};
        request.sorting = sorting;
        return this.httpService
            .post(this.createUrlWithMethodNameAndUrlParams(methodName, pageParameter, pageSizeParameter), request)
            .then(function (response: any): string {
                if (Variable.isNullOrUndefined(response)) {
                    throw new Error('ExportToExcel method returns not defined response.');
                }
                return response;
            });
    }
    /// helpers
    protected createEmptyEntity(): LeadEntity {
        return new LeadEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}
