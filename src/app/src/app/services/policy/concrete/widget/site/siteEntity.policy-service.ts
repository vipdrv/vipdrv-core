import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { SiteEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { ISiteEntityPolicyService } from './i-siteEntity.policy-service';
import { AbstractEntityPolicyService } from '../../../abstractEntity.policy-service';
import { permissionNames } from '../../../../../constants/index';
import { Variable } from '../../../../../utils/variable';

@Injectable()
export class SiteEntityPolicyService
    extends AbstractEntityPolicyService<SiteEntity>
    implements ISiteEntityPolicyService {
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('SiteEntityPolicyService: Service has been constructed.');
    }
    /// methods
    canUpdateContacts(): boolean {
        return true;
    }
    canUseWizard(entity: SiteEntity): boolean {
        return this.canUpdateEntity(entity);
    }
    canManageOtherOwnerSites(): boolean {
        return this.isGrantedPermission(permissionNames.canAllSite);
    }
    canImportVehicles(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllOwn) ||
            this.isGrantedPermission(permissionNames.canImportVehicles) ||
            this.isGrantedPermission(permissionNames.canImportOwnVehicles);
    }
    canImportVehiclesForEntity(entity: SiteEntity): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canImportVehicles) ||
            Variable.isNotNullOrUndefined(this.authService.currentUser) &&
            entity.userId === this.authService.currentUser.id &&
            (this.isGrantedPermission(permissionNames.canAllOwn) ||
            this.isGrantedPermission(permissionNames.canImportOwnVehicles));
    }

    canGet(): boolean {
        return true;
    }

    canCreate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllSite) ||
            this.isGrantedPermission(permissionNames.canCreateSite);
    }

    canUpdate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllSite) ||
            this.isGrantedPermission(permissionNames.canUpdateSite) ||
            this.isGrantedPermission(permissionNames.canAllOwn);
    }

    canDelete(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllSite) ||
            this.isGrantedPermission(permissionNames.canDeleteSite);
    }

    protected innerCanGetEntity(entity: SiteEntity): boolean {
        return true;
    }

    protected innerCanCreateEntity(entity: SiteEntity): boolean {
        return true;
    }

    protected innerCanUpdateEntity(entity: SiteEntity): boolean {
        const isOwnSite = true; // TODO: implement policy for onwSites

        return this.canUpdate() ||
            Variable.isNotNullOrUndefined(this.authService.currentUserId) &&
            isOwnSite;
    }

    protected innerCanDeleteEntity(entity: SiteEntity): boolean {
        const isOwnSite = true; // TODO: implement policy for onwSites

        return this.canDelete() ||
            this.isGrantedPermission(permissionNames.canDeleteOwnSite) &&
            Variable.isNotNullOrUndefined(this.authService.currentUserId) &&
            isOwnSite;
    }
}