import { Injectable } from '@angular/core';
import { HttpService, ConsoleLogger, Variable } from './../../../../utils/index';
import { LeadEntity, LightEntity } from './../../../../entities/index';
import { CRUDApiService } from './../../crud.api-service';
import { ILeadApiService } from './i-lead.api-service';
import { UrlParameter } from './../../urlParameter';
@Injectable()
export class LeadApiService extends CRUDApiService<LeadEntity, number, LightEntity> implements ILeadApiService {
    /// ctor
    constructor(
        httpService: HttpService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'lead');
    }
    /// methods
    exportToExcel(page: number, pageSize: number, sorting: string, filter: any): Promise<string> {
        let methodName = 'export/excel';
        let pageParameter = new UrlParameter('page', page);
        let pageSizeParameter = new UrlParameter('pageSize', pageSize);
        let request = Variable.isNotNullOrUndefined(filter) ? filter : {};
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
