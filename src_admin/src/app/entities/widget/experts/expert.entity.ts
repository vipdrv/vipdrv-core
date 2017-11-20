import { Variable, WorkingInterval } from './../../../utils/index';
import { Entity, IPassivable, IOrderable } from './../../index';
export class ExpertEntity extends Entity implements IPassivable, IOrderable {
    siteId: number;
    name: string;
    description: string;
    order: number;
    isActive: boolean;
    photoUrl: string;
    facebookUrl: string;
    linkedinUrl: string;
    workingHours: Array<WorkingInterval>;
    /// ctoe
    constructor() {
        super();
    }
    /// methods
    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        let mock: ExpertEntity = <ExpertEntity>dto;
        super.initializeFromDto(dto);
        this.siteId = mock.siteId;
        this.name = mock.name;
        this.description = mock.description;
        this.order = mock.order;
        this.isActive = mock.isActive;
        this.photoUrl = mock.photoUrl;
        this.facebookUrl = mock.facebookUrl;
        this.linkedinUrl = mock.linkedinUrl;
        this.workingHours = WorkingInterval.initializeManyFromDto(dto.workingHours);
    }
}