/// is used to provide access for actions with entity
export interface IAbstractPolicyService {
    canGet(): boolean;
    canCreate(): boolean;
    canUpdate(): boolean;
    canDelete(): boolean;
}