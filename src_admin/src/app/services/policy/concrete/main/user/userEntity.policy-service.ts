import { AbstractEntityPolicyService } from '../../../abstractEntity.policy-service';
import { UserEntity } from '../../../../../entities/main/users/user.entity';
import { IUserEntityPolicyService } from './i-userEntity.policy-service';
import { ConsoleLogger } from '../../../../../utils/logging/console/console.logger';
import { AuthorizationService } from '../../../../authorization/authorization.service';
import { permissionNames } from '../../../../../constants/permissions.consts';
import { Injectable } from '@angular/core';

@Injectable()
export class UserEntityPolicyService
    extends AbstractEntityPolicyService<UserEntity>
    implements IUserEntityPolicyService {

    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('UserEntityPolicyService: Service has been constructed.')
    }

    /// methods
    canGet(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllUser) ||
            this.isGrantedPermission(permissionNames.canRetrieveAll) ||
            this.isGrantedPermission(permissionNames.canRetrieveUser);
    }

    canCreate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllUser) ||
            this.isGrantedPermission(permissionNames.canCreateUser);
    }

    canUpdate(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllUser) ||
            this.isGrantedPermission(permissionNames.canUpdateUser);
    }

    canDelete(): boolean {
        return this.isGrantedPermission(permissionNames.canAllAll) ||
            this.isGrantedPermission(permissionNames.canAllUser) ||
            this.isGrantedPermission(permissionNames.canDeleteUser);
    }

    protected innerCanGetEntity(entity: UserEntity): boolean {
        return this.canGet();
    }

    protected innerCanCreateEntity(entity: UserEntity): boolean {
        return this.canCreate();
    }

    protected innerCanUpdateEntity(entity: UserEntity): boolean {
        return this.canUpdate();
    }

    protected innerCanDeleteEntity(entity: UserEntity): boolean {
        return this.canDelete();
    }
}
