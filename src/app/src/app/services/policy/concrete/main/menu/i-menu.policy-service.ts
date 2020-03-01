import { IAbstractPolicyService } from '../../../i-abstract.policy-service';

export interface IMenuPolicyService extends IAbstractPolicyService {
    canGetHome(): boolean;
    canGetSites(): boolean;
    canGetLeads(): boolean;
    canGetSettings(): boolean;
}