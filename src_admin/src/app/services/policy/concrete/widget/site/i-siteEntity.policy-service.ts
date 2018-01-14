import { SiteEntity } from './../../../../../entities/index';
import { IEntityPolicyService } from './../../../i-entity.policy-service';
/// is used to provide access for actions with entity site
export interface ISiteEntityPolicyService extends IEntityPolicyService<SiteEntity> {
    canUpdateContacts(): boolean;
    canUseWizard(entity: SiteEntity): boolean;
    canManageOtherOwnerSites(): boolean;
}
