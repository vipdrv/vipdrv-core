import { Injectable } from '@angular/core';
import { Variable, ConsoleLogger } from './../../../../../utils/index';
import { LeadEntity } from './../../../../../entities/index';
import { ObjectValidationService } from './../../../object.validation-service';
import { ILeadValidationService } from './i-lead.validation-service';
@Injectable()
export class LeadValidationService
    extends ObjectValidationService<LeadEntity>
    implements ILeadValidationService {
    /// ctor
    constructor(logger: ConsoleLogger) {
        super(logger);
        this.logger.logDebug('LeadValidationService: Service has been constructed.');
    }
    /// methods
    isValid(entity: LeadEntity): boolean {
        throw new Error('Not supported.');
    }
}