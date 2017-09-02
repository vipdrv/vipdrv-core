import { Injectable } from "@angular/core";
import { ISiteApiService } from "./i-site.api-service";
import { BaseApiService } from "./../base.api-service";
import { UrlParameter } from "./../urlParameter";
import { HttpService, ConsoleLogger } from "./../../../utils/index";
import { SiteEntity } from "./../../../entities/sites/site.entity";
@Injectable()
export class SiteApiService extends BaseApiService implements ISiteApiService {
    /// ctor
    constructor(
        httpService: HttpService,
        logger: ConsoleLogger) {
        super(httpService, logger, "site");
    }
    /// methods
    get(id: number): Promise<SiteEntity> {
        return this.httpService
            .get(this.createUrlWithMethodNameAndParams("", String(id)))
            .then(function (dto: SiteEntity): SiteEntity {
                let stub: SiteEntity = new SiteEntity();
                stub.initializeFromDto(dto);
                return stub;
            });
    }
    create(id: SiteEntity): Promise<SiteEntity> {
        return Promise.resolve(null);
    }
    update(id: SiteEntity): Promise<SiteEntity> {
        return Promise.resolve(null);
    }
    delete(id: number): Promise<void> {
        return Promise.resolve();
    }
}