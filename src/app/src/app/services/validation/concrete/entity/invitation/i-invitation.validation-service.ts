import { InvitationEntity } from './../../../../../entities/index';
import { IValidationService } from './../../../i-validation-service';
export interface IInvitationValidationService extends IValidationService<InvitationEntity> {
    isValidEmail(entity: InvitationEntity): boolean;
    isValidRole(entity: InvitationEntity): boolean;
    isValidAvailableSitesCount(entity: InvitationEntity): boolean;

    getInvalidEmailMessageKey(entity: InvitationEntity): string;
    getInvalidRoleMessageKey(entity: InvitationEntity): string;
    getInvalidAvailableSitesCountMessageKey(entity: InvitationEntity): string;
}