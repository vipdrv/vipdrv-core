import { Variable } from "./../../../utils/index";
import { Entity } from "./../../index";

export class UserEntity extends Entity {
    email: number;
    password: string;
    maxSitesCount: number;

    constructor() {
        super();
    }

    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        let mock: UserEntity = <UserEntity>dto;
        super.initializeFromDto(dto);
        this.email = mock.email;
        this.password = mock.password;
        this.maxSitesCount = mock.maxSitesCount;
    }
}