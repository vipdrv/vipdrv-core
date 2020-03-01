import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { ExpertEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { IExpertEntityPolicyService } from './i-expertEntity.policy-service';
import { AbstractEntityPolicyService } from '../../../abstractEntity.policy-service';
import { permissionNames } from '../../../../../constants/index';
import { Variable } from '../../../../../utils/index';

@Injectable()
export class ExpertEntityPolicyService
    extends AbstractEntityPolicyService<ExpertEntity>
    implements IExpertEntityPolicyService {
    /// injected dependencies
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('ExpertEntityPolicyService: Service has been constructed.');
    }
    /// methods
    canGet(): boolean {
        return true;
    }

    canCreate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllExpert) ||
            this.isGrantedPermission(permissionNames.canCreateExpert) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    canUpdate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllExpert) ||
            this.isGrantedPermission(permissionNames.canUpdateExpert) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    canDelete(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllExpert) ||
            this.isGrantedPermission(permissionNames.canDeleteExpert) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    protected innerCanGetEntity(entity: ExpertEntity): boolean {
        return true;
    }

    protected innerCanCreateEntity(entity: ExpertEntity): boolean {
        return true;
    }

    protected innerCanUpdateEntity(entity: ExpertEntity): boolean {
        const isExpertFromOwnSite = true; // TODO: implement policy for onwSites

        return this.canUpdate() ||
            Variable.isNotNullOrUndefined(this.authService.currentUserId) &&
            isExpertFromOwnSite;
    }

    protected innerCanDeleteEntity(entity: ExpertEntity): boolean {
        const isExpertFromOwnSite = true; // TODO: implement policy for onwSites

        return this.canDelete() ||
            Variable.isNotNullOrUndefined(this.authService.currentUserId) &&
            isExpertFromOwnSite;
    }

    canUpdateOrder(): boolean {
        return this.canUpdate();
    }

    canUpdateActivity(): boolean {
        return this.canUpdate();
    }

    canUpdateOrderForEntity(entity: ExpertEntity): boolean {
        return this.innerCanUpdateEntity(entity);
    }

    canUpdateActivityForEntity(entity: ExpertEntity): boolean {
        return this.innerCanUpdateEntity(entity);
    }
}