import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { InvitationEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { IInvitationEntityPolicyService } from './i-invitationEntity.policy-service';
import { AlwaysLoyalEntityPolicyService } from '../../../alwaysLoyalEntity.policy-service';
@Injectable()
export class InvitationEntityPolicyService
    extends AlwaysLoyalEntityPolicyService<InvitationEntity>
    implements IInvitationEntityPolicyService {
    /// injected dependencies
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('InvitationEntityPolicyService: Service has been constructed.');
    }
    /// methods
}