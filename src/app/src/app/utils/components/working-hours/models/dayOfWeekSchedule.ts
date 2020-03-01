import { Variable } from '../../../index';
import { WorkingInterval } from './workingInterval';
export class DayOfWeekSchedule extends WorkingInterval {
    /// fields
    isActive: boolean;
    /// ctor
    constructor() {
        super();
    }
    /// methods
    isValid(checkEdit: boolean = false): boolean {
        return this.innerIsValid(checkEdit);
    }
    initialize(dayOfWeek: number, startTime: string, endTime: string, isActive?: boolean): void {
        this.innerInitialize(dayOfWeek, startTime, endTime);
        if (Variable.isNotNullOrUndefined(isActive)) {
            this.isActive = isActive;
        }
    }
    /// static
    static initializeDefault(dayOfWeekNumber: number): DayOfWeekSchedule {
        if (dayOfWeekNumber < 0 || dayOfWeekNumber > 6) {
           throw new Error(
               'Argument exception (dayOfWeekNumber)! ' + 'Day of week number can not be ' + dayOfWeekNumber + '.');
        }
        const entity = new DayOfWeekSchedule();
        entity.initialize(dayOfWeekNumber, '09:00:00', '18:00:00');
        entity.isActive = !(dayOfWeekNumber === 0 || dayOfWeekNumber === 6);
        return entity;
    }
}