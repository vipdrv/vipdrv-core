import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { BeverageEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { IBeverageEntityPolicyService } from './i-beverageEntity.policy-service';
import { AlwaysLoyalEntityPolicyService } from '../../../alwaysLoyalEntity.policy-service';
@Injectable()
export class BeverageEntityPolicyService
    extends AlwaysLoyalEntityPolicyService<BeverageEntity>
    implements IBeverageEntityPolicyService {
    /// injected dependencies
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('BeverageEntityPolicyService: Service has been constructed.');
    }
    /// methods
    canUpdateOrder(): boolean {
        return true;
    }
    canUpdateActivity(): boolean {
        return true;
    }
    canUpdateOrderForEntity(entity: BeverageEntity): boolean {
        return true && this.canUpdateOrder();
    }
    canUpdateActivityForEntity(entity: BeverageEntity): boolean {
        return true && this.canUpdateActivity();
    }
}