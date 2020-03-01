import { RouteEntity } from './../../../../../entities/index';
import { IEntityPolicyService } from './../../../i-entity.policy-service';
/// is used to provide access for actions with entity route
export interface IRouteEntityPolicyService extends IEntityPolicyService<RouteEntity> {
    canUpdateOrder(): boolean;
    canUpdateActivity(): boolean;
    canUpdateOrderForEntity(entity: RouteEntity): boolean;
    canUpdateActivityForEntity(entity: RouteEntity): boolean;
}