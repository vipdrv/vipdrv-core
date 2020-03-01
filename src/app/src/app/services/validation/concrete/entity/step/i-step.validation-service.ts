import { StepEntity } from './../../../../../entities/index';
import { IValidationService } from './../../../i-validation-service';

export interface IStepValidationService extends IValidationService<StepEntity> {
    isValidName(entity: StepEntity): boolean;
    getInvalidNameMessageKey(entity: StepEntity): string;
}