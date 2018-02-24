import { Variable, WorkingInterval } from './../../../utils/index';
import { Entity, IPassivable, IOrderable } from './../../index';
export class ExpertEntity extends Entity implements IPassivable, IOrderable {
    siteId: number;
    photoUrl: string;
    name: string;
    title: string;
    description: string;
    email: string;
    phoneNumber: string;
    facebookUrl: string;
    linkedinUrl: string;
    workingHours: Array<WorkingInterval>;
    order: number;
    isActive: boolean;
    isPartOfTeamNewCars: boolean;
    isPartOfTeamUsedCars: boolean;
    isPartOfTeamCPO: boolean;
    /// ctoe
    constructor() {
        super();
    }
    /// methods
    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        const mock: ExpertEntity = <ExpertEntity>dto;
        super.initializeFromDto(dto);
        this.siteId = mock.siteId;
        this.photoUrl = mock.photoUrl;
        this.name = mock.name;
        this.title = mock.title;
        this.description = mock.description;
        this.email = mock.email;
        this.phoneNumber = mock.phoneNumber;
        this.facebookUrl = mock.facebookUrl;
        this.linkedinUrl = mock.linkedinUrl;
        this.workingHours = WorkingInterval.initializeManyFromDto(dto.workingHours);
        this.order = mock.order;
        this.isActive = mock.isActive;
        this.isPartOfTeamNewCars = mock.isPartOfTeamNewCars;
        this.isPartOfTeamUsedCars = mock.isPartOfTeamUsedCars;
        this.isPartOfTeamCPO = mock.isPartOfTeamCPO;
    }
}