import { Injectable } from '@angular/core';
import { Variable, ConsoleLogger } from './../../../../../utils/index';
import { ExpertEntity } from './../../../../../entities/index';
import { ObjectValidationService } from './../../../object.validation-service';
import { IExpertValidationService } from './i-expert.validation-service';
@Injectable()
export class ExpertValidationService
    extends ObjectValidationService<ExpertEntity>
    implements IExpertValidationService {
    /// ctor
    constructor(logger: ConsoleLogger) {
        super(logger);
        this.logger.logDebug('ExpertValidationService: Service has been constructed.');
    }
    /// methods
    isValid(entity: ExpertEntity): boolean {
        return true;
    }
}