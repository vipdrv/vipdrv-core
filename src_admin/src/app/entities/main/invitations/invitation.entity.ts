import { Variable, Extensions } from './../../../utils/index';
import { Entity } from './../../entity';
export class InvitationEntity extends Entity {
    invitationCode: string;
    email: string;
    phoneNumber: string;
    createdTimeUtc: string;
    used: boolean;
    usedTimeUtc: string;
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
        const mock: InvitationEntity = <InvitationEntity>dto;
        super.initializeFromDto(dto);
        this.invitationCode = mock.invitationCode;
        this.email = mock.email;
        this.phoneNumber = mock.phoneNumber;
        this.createdTimeUtc = Variable.isNullOrUndefined(mock.createdTimeUtc) ?
            null : Extensions.formatUtcDateTimeToLocalTimezone(mock.createdTimeUtc);
        this.used = mock.used;
        this.usedTimeUtc = Variable.isNullOrUndefined(mock.usedTimeUtc) ?
            null : Extensions.formatUtcDateTimeToLocalTimezone(mock.usedTimeUtc);
        this.availableSitesCount = mock.availableSitesCount;
        this.roleId = mock.roleId;
        this.role = mock.role;
        this.invitatorId = mock.invitatorId;
        this.invitator = mock.invitator;
    }
}
