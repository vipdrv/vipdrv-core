import { ILogger } from './../../utils/index';
import { IAuthorizationService } from './../index';
import { IEntityPolicyService } from './i-entity.policy-service';
export abstract class AbstractEntityPolicyService<TEntity> implements IEntityPolicyService<TEntity> {
    /// injected dependencies
    protected authService: IAuthorizationService;
    protected logger: ILogger;
    /// ctor
    constructor(logger: ILogger, authService: IAuthorizationService) {
        this.logger = logger;
        this.authService = authService;
    }
    /// methods
    abstract canGet(): boolean;
    abstract canCreate(): boolean;
    abstract canUpdate(): boolean;
    abstract canDelete(): boolean;
    canGetEntity(entity: TEntity): boolean {
        return this.canGet() && this.innerCanGetEntity(entity);
    }
    canCreateEntity(entity: TEntity): boolean {
        return this.canGet() && this.innerCanCreateEntity(entity);
    }
    canUpdateEntity(entity: TEntity): boolean {
        return this.canGet() && this.innerCanUpdateEntity(entity);
    }
    canDeleteEntity(entity: TEntity): boolean {
        return this.canGet() && this.innerCanDeleteEntity(entity);
    }
    /// helpers
    protected abstract innerCanGetEntity(entity: TEntity): boolean;
    protected abstract innerCanCreateEntity(entity: TEntity): boolean;
    protected abstract innerCanUpdateEntity(entity: TEntity): boolean;
    protected abstract innerCanDeleteEntity(entity: TEntity): boolean;
    // Is used to check if permission is granted for current User
    protected isGrantedPermission(permission: string): boolean {
        return this.authService.currentUserPermissions.indexOf(permission) > -1;
    }
}