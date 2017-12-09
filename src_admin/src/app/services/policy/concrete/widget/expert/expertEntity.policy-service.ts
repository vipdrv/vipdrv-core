import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { ExpertEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { IExpertEntityPolicyService } from './i-expertEntity.policy-service';
import { AlwaysLoyalEntityPolicyService } from '../../../alwaysLoyalEntity.policy-service';
@Injectable()
export class ExpertEntityPolicyService
    extends AlwaysLoyalEntityPolicyService<ExpertEntity>
    implements IExpertEntityPolicyService {
    /// injected dependencies
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('ExpertEntityPolicyService: Service has been constructed.');
    }
    /// methods
    canUpdateOrder(): boolean {
        return true;
    }
    canUpdateActivity(): boolean {
        return true;
    }
    canUpdateOrderForEntity(entity: ExpertEntity): boolean {
        return true && this.canUpdateOrder();
    }
    canUpdateActivityForEntity(entity: ExpertEntity): boolean {
        return true && this.canUpdateActivity();
    }
}