import { Variable } from './../../../utils/index';
import { Entity } from './../../index';
export class RoleEntity extends Entity {
    name: string;
    canBeUsedForInvitation: boolean;

    /// ctor
    constructor() {
        super();
    }

    /// methods
    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        const mock: RoleEntity = <RoleEntity>dto;
        super.initializeFromDto(dto);
        this.name = mock.name;
        this.canBeUsedForInvitation = mock.canBeUsedForInvitation;
    }
}
