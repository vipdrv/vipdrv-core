import { Variable } from "./../../../utils/index";
import { Entity, IPassivable, IOrderable } from "./../../index";

export class BeverageEntity extends Entity implements IPassivable, IOrderable {
    siteId: number;
    name: string;
    description: string;
    photoUrl: string;
    order: number;
    isActive: boolean;

    constructor() {
        super();
    }

    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        let mock: BeverageEntity = <BeverageEntity>dto;
        super.initializeFromDto(dto);
        this.siteId = mock.siteId;
        this.name = mock.name;
        this.description = mock.description;
        this.photoUrl = mock.photoUrl;
        this.order = mock.order;
        this.isActive = mock.isActive;
    }
}