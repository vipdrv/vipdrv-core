import { Injectable } from '@angular/core';
import { Variable, ConsoleLogger } from './../../../../../utils/index';
import { RouteEntity } from './../../../../../entities/index';
import { ObjectValidationService } from './../../../object.validation-service';
import { IRouteValidationService } from './i-route.validation-service';
@Injectable()
export class RouteValidationService
    extends ObjectValidationService<RouteEntity>
    implements IRouteValidationService {
    /// ctor
    constructor(logger: ConsoleLogger) {
        super(logger);
        this.logger.logDebug('RouteValidationService: Service has been constructed.');
    }
    /// methods
    isValid(entity: RouteEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            this.isValidName(entity) &&
            this.isValidDescription(entity);
    }
    isValidName(entity: RouteEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.name);
    }
    isValidDescription(entity: RouteEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.description);
    }
    getInvalidNameMessageKey(entity: RouteEntity): string {
        return 'validation.routes.name';
    }
    getInvalidDescriptionMessageKey(entity: RouteEntity): string {
        return 'validation.routes.description';
    }
}