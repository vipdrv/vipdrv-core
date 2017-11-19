import { ILogger } from './../../utils/index';
import { IAuthorizationService } from './../index';
import { IEntityPolicyService } from './i-entity.policy-service';
import { AbstractEntityPolicyService } from './abstractEntity.policy-service';
export abstract class AlwaysLoyalEntityPolicyService<TEntity>
    extends AbstractEntityPolicyService<TEntity>
    implements IEntityPolicyService<TEntity> {
    /// injected dependencies
    /// ctor
    constructor(logger: ILogger, authService: IAuthorizationService) {
        super(logger, authService);
    }
    /// methods
    canGet(): boolean {
        return true;
    }
    canCreate(): boolean {
        return true;
    }
    canUpdate(): boolean {
        return true;
    }
    canDelete(): boolean {
        return true;
    }
    /// helpers
    protected innerCanGetEntity(entity: TEntity): boolean {
        return true;
    }
    protected innerCanCreateEntity(entity: TEntity): boolean {
        return true;
    }
    protected innerCanUpdateEntity(entity: TEntity): boolean {
        return true;
    }
    protected innerCanDeleteEntity(entity: TEntity): boolean {
        return true;
    }
}