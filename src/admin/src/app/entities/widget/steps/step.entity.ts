import { Variable } from './../../../utils/index';
import { Entity, IPassivable, IOrderable } from './../../index';

export class StepEntity extends Entity implements IPassivable, IOrderable {
    /// entity properties
    siteId: number;
    descriptor: string;
    name: string;
    order: number;
    isActive: boolean;

    /// ctor
    constructor() {
        super();
    }
    /// methods
    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        const mock: StepEntity = <StepEntity>dto;
        super.initializeFromDto(dto);
        this.siteId = mock.siteId;
        this.descriptor = mock.descriptor;
        this.name = mock.name;
        this.order = mock.order;
        this.isActive = mock.isActive;
    }
}