import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { StepEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { IStepEntityPolicyService } from './i-stepEntity.policy-service';
import { AbstractEntityPolicyService } from '../../../abstractEntity.policy-service';
import { permissionNames } from '../../../../../constants/index';
import { Variable } from '../../../../../utils/variable';

@Injectable()
export class StepEntityPolicyService
    extends AbstractEntityPolicyService<StepEntity>
    implements IStepEntityPolicyService {

    /// injected dependencies
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('StepEntityPolicyService: Service has been constructed.');
    }
    /// methods
    canGet(): boolean {
        return true;
    }

    canCreate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllStep) ||
            this.isGrantedPermission(permissionNames.canCreateStep) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    canUpdate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllStep) ||
            this.isGrantedPermission(permissionNames.canUpdateStep) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    canDelete(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllStep) ||
            this.isGrantedPermission(permissionNames.canDeleteStep) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    protected innerCanGetEntity(entity: StepEntity): boolean {
        return true;
    }

    protected innerCanCreateEntity(entity: StepEntity): boolean {
        return true;
    }

    protected innerCanUpdateEntity(entity: StepEntity): boolean {
        const isStepFromOwnSite = true;

        return this.canUpdate() ||
            Variable.isNotNullOrUndefined(this.authService.currentUserId) &&
            isStepFromOwnSite;
    }

    protected innerCanDeleteEntity(entity: StepEntity): boolean {
        const isStepFromOwnSite = true; // TODO: implement policy for onwSites

        return this.canDelete() ||
            Variable.isNotNullOrUndefined(this.authService.currentUserId) &&
            isStepFromOwnSite;
    }
    canUpdateOrder(): boolean {
        return true;
    }
    canUpdateActivity(): boolean {
        return true;
    }
    canUpdateOrderForEntity(entity: StepEntity): boolean {
        return this.canUpdateOrder();
    }
    canUpdateActivityForEntity(entity: StepEntity): boolean {
        return this.canUpdateActivity();
    }
}