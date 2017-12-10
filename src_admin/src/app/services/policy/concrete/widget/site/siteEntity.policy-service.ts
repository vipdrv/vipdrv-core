import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { SiteEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { ISiteEntityPolicyService } from './i-siteEntity.policy-service';
import { AlwaysLoyalEntityPolicyService } from '../../../alwaysLoyalEntity.policy-service';
@Injectable()
export class SiteEntityPolicyService
    extends AlwaysLoyalEntityPolicyService<SiteEntity>
    implements ISiteEntityPolicyService {
    /// injected dependencies
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('SiteEntityPolicyService: Service has been constructed.');
    }
    /// methods
    canUpdateContacts(): boolean {
        return true;
    }
}