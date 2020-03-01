import { StepEntity } from './../../../../../entities/index';
import { IEntityPolicyService } from './../../../i-entity.policy-service';
/// is used to provide access for actions with entity step
export interface IStepEntityPolicyService extends IEntityPolicyService<StepEntity> {
    canUpdateOrder(): boolean;
    canUpdateActivity(): boolean;
    canUpdateOrderForEntity(entity: StepEntity): boolean;
    canUpdateActivityForEntity(entity: StepEntity): boolean;
}