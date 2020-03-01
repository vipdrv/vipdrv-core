import { Variable, Extensions } from '../../../index';
import { daysOfWeek } from '../../../../constants/index';
export class WorkingInterval {
    // main fields
    dayOfWeek: number;
    startTime: string;
    endTime: string;
    // service fields
    isEditProcessing: boolean;
    editDayOfWeek: number;
    editStartTime: string;
    editEndTime: string;
    /// ctor
    constructor() { }
    /// methods
    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        const mock: WorkingInterval = <WorkingInterval>dto;
        this.initialize(mock.dayOfWeek, mock.startTime, mock.endTime);
    }
    initialize(dayOfWeek: number, startTime: string, endTime: string): void {
        this.innerInitialize(dayOfWeek, startTime, endTime);
    }
    /// methods
    startEdit(): void {
        this.isEditProcessing = true;
    }
    commitEdit(): void {
        this.dayOfWeek = this.editDayOfWeek;
        this.startTime = this.editStartTime;
        this.endTime = this.editEndTime;
        this.isEditProcessing = false;
    }
    undoEdit(): void {
        this.isEditProcessing = false;
    }
    getDayOfWeekEntity(): { id: number; localizationKey: string; } {
        const daysOfWeekFiltered: Array<{ id: number; localizationKey: string; }> = daysOfWeek
            .filter((r) => r.id === this.dayOfWeek);
        return daysOfWeekFiltered.length === 1 ? daysOfWeekFiltered[0] : daysOfWeek[0];
    }
    isValid(checkEdit: boolean = false): boolean {
        return this.innerIsValid(checkEdit);
    }
    /// predicates
    protected innerIsValid(checkEdit: boolean = false): boolean {
        return this.dayOfWeek >= 0 && this.dayOfWeek <= 6 &&
            Extensions.regExp.time.test(this.startTime) &&
            Extensions.regExp.time.test(this.endTime) &&
            (
                checkEdit ||
                this.editDayOfWeek >= 0 && this.editDayOfWeek <= 6 &&
                Extensions.regExp.time.test(this.editStartTime) &&
                Extensions.regExp.time.test(this.editEndTime)
            );
    }
    protected innerInitialize(dayOfWeek: number, startTime: string, endTime: string): void {
        this.dayOfWeek = dayOfWeek;
        this.startTime = startTime;
        this.endTime = endTime;
        this.isEditProcessing = false;
        this.editDayOfWeek = this.dayOfWeek;
        this.editStartTime = this.startTime;
        this.editEndTime = this.endTime;
    }
    /// static
    static initializeManyFromDto = function (dtos: Array<any>): Array<WorkingInterval> {
        const result: Array<WorkingInterval> = [];
        if (Variable.isNotNullOrUndefined(dtos) && dtos.length) {
            for (let dto of dtos) {
                const entity = new WorkingInterval();
                entity.initializeFromDto(dto);
                result.push(entity);
            }
        }
        return result;
    }
}