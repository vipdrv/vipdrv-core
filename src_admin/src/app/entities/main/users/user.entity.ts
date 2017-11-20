import { Variable } from './../../../utils/index';
import { Entity } from './../../index';

export class UserEntity extends Entity {
    email: string;
    password: string;
    maxSitesCount: number;

    username: string;
    phoneNumber: string;
    firstName: string;
    secondName: string;
    avatarUrl: string;
    currentSitesCount: number;

/// ctor
    constructor() {
        super();
    }
    /// methods
    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        const mock: UserEntity = <UserEntity>dto;
        super.initializeFromDto(dto);
        this.email = mock.email;
        this.password = mock.password;
        this.maxSitesCount = mock.maxSitesCount;

        this.username = mock.username;
        this.phoneNumber = mock.phoneNumber;
        this.firstName = mock.firstName;
        this.secondName = mock.secondName;
        this.avatarUrl = mock.avatarUrl;
        this.currentSitesCount = mock.currentSitesCount;
    }
}
