import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { BeverageEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { IBeverageEntityPolicyService } from './i-beverageEntity.policy-service';
import { AbstractEntityPolicyService } from '../../../abstractEntity.policy-service';
import { permissionNames } from '../../../../../constants/index';
import { Variable } from '../../../../../utils/variable';

@Injectable()
export class BeverageEntityPolicyService
    extends AbstractEntityPolicyService<BeverageEntity>
    implements IBeverageEntityPolicyService {

    /// injected dependencies
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('BeverageEntityPolicyService: Service has been constructed.');
    }
    /// methods
    canGet(): boolean {
        return true;
    }

    canCreate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllBeverage) ||
            this.isGrantedPermission(permissionNames.canCreateBeverage) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    canUpdate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllBeverage) ||
            this.isGrantedPermission(permissionNames.canUpdateBeverage) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    canDelete(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllBeverage) ||
            this.isGrantedPermission(permissionNames.canDeleteBeverage) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    protected innerCanGetEntity(entity: BeverageEntity): boolean {
        return true;
    }

    protected innerCanCreateEntity(entity: BeverageEntity): boolean {
        return true;
    }

    protected innerCanUpdateEntity(entity: BeverageEntity): boolean {
        const isBeverageFromOwnSite = true; // TODO: implement policy for onwSites

        return this.canUpdate() ||
            Variable.isNotNullOrUndefined(this.authService.currentUserId) &&
            isBeverageFromOwnSite;
    }

    protected innerCanDeleteEntity(entity: BeverageEntity): boolean {
        const isBeverageFromOwnSite = true; // TODO: implement policy for onwSites

        return this.canDelete() ||
            Variable.isNotNullOrUndefined(this.authService.currentUserId) &&
            isBeverageFromOwnSite;
    }
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