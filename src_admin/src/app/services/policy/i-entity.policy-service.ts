import { IAbstractPolicyService } from './i-abstract.policy-service';
/// is used to provide access for actions with entity
export interface IEntityPolicyService<TEntity> extends IAbstractPolicyService {
    canGetEntity(entity: TEntity): boolean;
    canCreateEntity(entity: TEntity): boolean;
    canUpdateEntity(entity: TEntity): boolean;
    canDeleteEntity(entity: TEntity): boolean;
}
