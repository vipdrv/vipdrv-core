import { BeverageEntity } from './../../../../../entities/index';
import { IValidationService } from './../../../i-validation-service';
export interface IBeverageValidationService extends IValidationService<BeverageEntity> {
    isValidName(entity: BeverageEntity): boolean;
    isValidDescription(entity: BeverageEntity): boolean;
    getInvalidNameMessageKey(entity: BeverageEntity): string;
    getInvalidDescriptionMessageKey(entity: BeverageEntity): string;
}