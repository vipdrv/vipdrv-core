import { Injectable } from '@angular/core';
import { Variable, ConsoleLogger } from './../../../../../utils/index';
import { SiteEntity } from './../../../../../entities/index';
import { ObjectValidationService } from './../../../object.validation-service';
import { ISiteValidationService } from './i-site.validation-service';
@Injectable()
export class SiteValidationService
    extends ObjectValidationService<SiteEntity>
    implements ISiteValidationService {
    /// ctor
    constructor(logger: ConsoleLogger) {
        super(logger);
        this.logger.logDebug('SiteValidationService: Service has been constructed.');
    }
    /// methods
    isValid(entity: SiteEntity): boolean {
        return this.isNameValid(entity) &&
            this.isUrlValid(entity) &&
            this.isImageUrlValid(entity);
    }
    isNameValid(entity: SiteEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.name);
    }
    isUrlValid(entity: SiteEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.url);
    }
    isImageUrlValid(entity: SiteEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.imageUrl);
    }
    getInvalidNameMessageKey(entity: SiteEntity): string {
        return 'validation.sites.invalidNameMessage';
    }
    getInvalidUrlMessageKey(entity: SiteEntity): string {
        return 'validation.sites.invalidUrlMessage';
    }
}