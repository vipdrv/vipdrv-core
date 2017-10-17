import { Variable } from './../../../utils/index';
import { Entity } from './../../index';

export class UserEntity extends Entity {
    email: string;
    password: string;
    maxSitesCount: number;
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
    }
}
