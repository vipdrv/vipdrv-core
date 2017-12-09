import { Injectable } from '@angular/core';
import { Variable, Extensions, ConsoleLogger } from './../../../../../utils/index';
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
        return this.isValidEmail(entity) && this.isValidAvailableSitesCount(entity);
    }
    isValidEmail(entity: InvitationEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) && Extensions.regExp.email.test(entity.email);
    }
    isValidAvailableSitesCount(entity: InvitationEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) && entity.availableSitesCount > 0;
    }
    getInvalidEmailMessageKey(entity: InvitationEntity): string {
        return 'validation.invitations.invalidEmailMessage';
    }
    getInvalidAvailableSitesCountMessageKey(entity: InvitationEntity): string {
        return 'validation.invitations.invalidAvailableSitesCount';
    }
}