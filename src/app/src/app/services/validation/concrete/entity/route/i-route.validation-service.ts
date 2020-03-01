import { RouteEntity } from './../../../../../entities/index';
import { IValidationService } from './../../../i-validation-service';
export interface IRouteValidationService extends IValidationService<RouteEntity> {
    isValidName(entity: RouteEntity): boolean;
    isValidDescription(entity: RouteEntity): boolean;
    getInvalidNameMessageKey(entity: RouteEntity): string;
    getInvalidDescriptionMessageKey(entity: RouteEntity): string;
}