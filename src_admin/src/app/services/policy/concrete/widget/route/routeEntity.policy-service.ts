import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { RouteEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { IRouteEntityPolicyService } from './i-routeEntity.policy-service';
import { AlwaysLoyalEntityPolicyService } from '../../../alwaysLoyalEntity.policy-service';
@Injectable()
export class RouteEntityPolicyService
    extends AlwaysLoyalEntityPolicyService<RouteEntity>
    implements IRouteEntityPolicyService {
    /// injected dependencies
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('RouteEntityPolicyService: Service has been constructed.');
    }
    /// methods
}