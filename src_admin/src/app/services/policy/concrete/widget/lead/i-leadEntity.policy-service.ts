import { LeadEntity } from './../../../../../entities/index';
import { IEntityPolicyService } from './../../../i-entity.policy-service';
/// is used to provide access for actions with entity site
export interface ILeadEntityPolicyService extends IEntityPolicyService<LeadEntity> {
    canExportDataToExcel(): boolean;
}
