import { ExpertEntity } from './../../../../../entities/index';
import { IEntityPolicyService } from './../../../i-entity.policy-service';
/// is used to provide access for actions with entity expert
export interface IExpertEntityPolicyService extends IEntityPolicyService<ExpertEntity> {
}