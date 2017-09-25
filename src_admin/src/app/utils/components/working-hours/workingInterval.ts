import { Variable } from '../../index';
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
        let mock: WorkingInterval = <WorkingInterval>dto;
        this.initialize(mock.dayOfWeek, mock.startTime, mock.endTime);
    }
    initialize(dayOfWeek: number, startTime: string, endTime: string): void {
        this.dayOfWeek = dayOfWeek;
        this.startTime = startTime;
        this.endTime = endTime;
        this.isEditProcessing = false;
        this.editDayOfWeek = this.dayOfWeek;
        this.editStartTime = this.startTime;
        this.editEndTime = this.endTime;
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
    /// static
    static initializeManyFromDto = function (dtos: Array<any>): Array<WorkingInterval> {
        let result: Array<WorkingInterval> = [];
        if (Variable.isNotNullOrUndefined(dtos) && dtos.length) {
            for (let dto of dtos) {
                let entity = new WorkingInterval();
                entity.initializeFromDto(dto);
                result.push(entity);
            }
        }
        return result;
    }
}