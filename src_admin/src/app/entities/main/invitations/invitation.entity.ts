import { Variable, Extensions } from './../../../utils/index';
import { Entity } from './../../entity';
export class InvitationEntity extends Entity {
    invitationCode: string;
    email: string;
    phoneNumber: string;
    createdTime: string;
    used: boolean;
    usedTime: string;
    availableSitesCount: number;
    roleId: number;
    role: string;
    invitatorId: string;
    invitator: string;
    /// ctor
    constructor() {
        super();
    }
    /// methods
    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        super.initializeFromDto(dto);
        this.invitationCode = dto.invitationCode;
        this.email = dto.email;
        this.phoneNumber = dto.phoneNumber;
        this.createdTime = Variable.isNullOrUndefined(dto.createdTimeUtc) ?
            null : Extensions.formatUtcDateTimeToLocalTimezone(dto.createdTimeUtc);
        this.used = dto.used;
        this.usedTime = Variable.isNullOrUndefined(dto.usedTimeUtc) ?
            null : Extensions.formatUtcDateTimeToLocalTimezone(dto.usedTimeUtc);
        this.availableSitesCount = dto.availableSitesCount;
        this.roleId = dto.roleId;
        this.role = dto.role;
        this.invitatorId = dto.invitatorId;
        this.invitator = dto.invitator;
    }
}
