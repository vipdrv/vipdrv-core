import { BeverageEntity } from './../../../../../entities/index';
import { IEntityPolicyService } from './../../../i-entity.policy-service';
/// is used to provide access for actions with entity beverage
export interface IBeverageEntityPolicyService extends IEntityPolicyService<BeverageEntity> {
}