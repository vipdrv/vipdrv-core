import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { RouteEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { IRouteEntityPolicyService } from './i-routeEntity.policy-service';
import { permissionNames } from '../../../../../constants/index';
import { Variable } from '../../../../../utils/variable';
import { AbstractEntityPolicyService } from '../../../abstractEntity.policy-service';

@Injectable()
export class RouteEntityPolicyService
    extends AbstractEntityPolicyService<RouteEntity>
    implements IRouteEntityPolicyService {

    /// injected dependencies
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('RouteEntityPolicyService: Service has been constructed.');
    }
    /// methods
    canGet(): boolean {
        return true;
    }

    canCreate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllRoute) ||
            this.isGrantedPermission(permissionNames.canCreateRoute) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    canUpdate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllRoute) ||
            this.isGrantedPermission(permissionNames.canUpdateRoute) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    canDelete(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllRoute) ||
            this.isGrantedPermission(permissionNames.canDeleteRoute) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    protected innerCanGetEntity(entity: RouteEntity): boolean {
        return true;
    }

    protected innerCanCreateEntity(entity: RouteEntity): boolean {
        return true;
    }

    protected innerCanUpdateEntity(entity: RouteEntity): boolean {
        const isRouteFromOwnSite = true; // TODO: implement policy for onwSites

        return this.canUpdate() ||
            Variable.isNotNullOrUndefined(this.authService.currentUserId) &&
            isRouteFromOwnSite;
    }

    protected innerCanDeleteEntity(entity: RouteEntity): boolean {
        const isRouteFromOwnSite = true; // TODO: implement policy for onwSites

        return this.canDelete() ||
            Variable.isNotNullOrUndefined(this.authService.currentUserId) &&
            isRouteFromOwnSite;
    }
    canUpdateOrder(): boolean {
        return true;
    }
    canUpdateActivity(): boolean {
        return true;
    }
    canUpdateOrderForEntity(entity: RouteEntity): boolean {
        return true && this.canUpdateOrder();
    }
    canUpdateActivityForEntity(entity: RouteEntity): boolean {
        return true && this.canUpdateActivity();
    }

}