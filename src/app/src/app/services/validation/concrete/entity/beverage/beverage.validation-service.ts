import { Injectable } from '@angular/core';
import { Variable, ConsoleLogger } from './../../../../../utils/index';
import { BeverageEntity } from './../../../../../entities/index';
import { ObjectValidationService } from './../../../object.validation-service';
import { IBeverageValidationService } from './i-beverage.validation-service';
@Injectable()
export class BeverageValidationService
    extends ObjectValidationService<BeverageEntity>
    implements IBeverageValidationService {
    /// ctor
    constructor(logger: ConsoleLogger) {
        super(logger);
        this.logger.logDebug('BeverageValidationService: Service has been constructed.');
    }
    /// methods
    isValid(entity: BeverageEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            this.isValidName(entity) &&
            this.isValidDescription(entity);
    }
    isValidName(entity: BeverageEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.name);
    }
    isValidDescription(entity: BeverageEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.description);
    }
    getInvalidNameMessageKey(entity: BeverageEntity): string {
        return 'validation.beverages.name';
    }
    getInvalidDescriptionMessageKey(entity: BeverageEntity): string {
        return 'validation.beverages.description';
    }
}