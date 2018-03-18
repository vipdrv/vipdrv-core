import { Injectable } from '@angular/core';
import { Extensions, Variable, ConsoleLogger } from './../../../../../utils/index';
import { StepEntity } from './../../../../../entities/index';
import { ObjectValidationService } from './../../../object.validation-service';
import { IStepValidationService } from './i-step.validation-service';
@Injectable()
export class StepValidationService
    extends ObjectValidationService<StepEntity>
    implements IStepValidationService {
    /// ctor
    constructor(logger: ConsoleLogger) {
        super(logger);
        this.logger.logDebug('StepValidationService: Service has been constructed.');
    }
    /// methods
    isValid(entity: StepEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            this.isValidName(entity);
    }
    isValidName(entity: StepEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.name);
    }
    getInvalidNameMessageKey(entity: StepEntity): string {
        return 'validation.steps.name';
    }
}