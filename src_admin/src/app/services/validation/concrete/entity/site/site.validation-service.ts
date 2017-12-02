import { Injectable } from '@angular/core';
import { Variable, ILogger, ConsoleLogger } from './../../../../../utils/index';
import { SiteEntity } from './../../../../../entities/index';
import { ISiteValidationService } from './i-site.validation-service';
@Injectable()
export class SiteValidationService implements ISiteValidationService {
    /// injected dependencies
    protected logger: ILogger;
    /// ctor
    constructor(logger: ConsoleLogger) {
        this.logger = logger;
        this.logger.logDebug('SiteValidationService: Service has been constructed.');
    }
    /// methods
    isValid(entity: SiteEntity): boolean {
        return this.isNameValid(entity) && this.isUrlValid(entity);
    }
    isNameValid(entity: SiteEntity): boolean {
        return true;
    }
    isUrlValid(entity: SiteEntity): boolean {
        return true;
    }
    getInvalidNameMessageKey(entity: SiteEntity): string {
        return 'validation.sites.invalidNameMessage';
    }
    getInvalidUrlMessageKey(entity: SiteEntity): string {
        return 'validation.sites.invalidUrlMessage';
    }
}