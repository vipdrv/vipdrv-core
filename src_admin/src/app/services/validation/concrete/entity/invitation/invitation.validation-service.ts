import { Injectable } from '@angular/core';
import { Variable, ConsoleLogger } from './../../../../../utils/index';
import { InvitationEntity } from './../../../../../entities/index';
import { ObjectValidationService } from './../../../object.validation-service';
import { IInvitationValidationService } from './i-invitation.validation-service';
@Injectable()
export class InvitationValidationService
    extends ObjectValidationService<InvitationEntity>
    implements IInvitationValidationService {
    /// ctor
    constructor(logger: ConsoleLogger) {
        super(logger);
        this.logger.logDebug('InvitationValidationService: Service has been constructed.');
    }
    /// methods
    isValid(entity: InvitationEntity): boolean {
        return true;
    }
}