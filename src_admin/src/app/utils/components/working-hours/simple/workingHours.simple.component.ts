import { Component, Input, Output, EventEmitter, OnInit, OnChanges, SimpleChanges, SimpleChange } from '@angular/core';
import { Variable } from '../../../index';
import { daysOfWeek } from '../../../../constants/index';
import { ApplicationConstants } from './../../../../app.constants';
import { WorkingInterval } from '../models/workingInterval';
import { DayOfWeekSchedule } from '../models/dayOfWeekSchedule';
@Component({
    selector: 'working-hours-simple',
    styleUrls: ['./workingHours.simple.scss'],
    templateUrl: './workingHours.simple.html',
})
export class WorkingHoursSimpleComponent implements OnInit, OnChanges {
    @Input() workingHours: Array<WorkingInterval>;
    @Output() workingHoursChanged: EventEmitter<Array<WorkingInterval>> = new EventEmitter<Array<WorkingInterval>>();
    protected actualEntities: Array<DayOfWeekSchedule>;
    protected switcherSettings = ApplicationConstants.switcherSettings;
    private _isSaveProcessing: boolean = false;
    constructor() { }
    ngOnInit(): void {
        this.undoChanges();
        this.submitWorkingHours();
    }
    ngOnChanges(changes: SimpleChanges) {
        const workingHoursChange: SimpleChange = changes['workingHours'];
        if (Variable.isNotNullOrUndefined(workingHoursChange) &&
            this.actualEntities !== this.workingHours) {
            this.undoChanges();
        }
    }
    protected submitWorkingHours(): void {
        this._isSaveProcessing = true;
        this.workingHoursChanged.emit(this.actualEntities.filter((r) => r.isActive));
        this._isSaveProcessing = false;
    }
    protected undoChanges(): void {
        this._isSaveProcessing = true;
        this.initializeDayOfWeekIntervals();
        this._isSaveProcessing = false;
    }
    /// predicates
    protected isSaveProcessing(): boolean {
        return this._isSaveProcessing;
    }
    /// helpers
    protected initializeDayOfWeekIntervals() {
        this.actualEntities = [];
        for (const dayOfWeek of daysOfWeek) {
            const entity = DayOfWeekSchedule.initializeDefault(dayOfWeek.id);
            if (Variable.isNotNullOrUndefined(this.workingHours)) {
                const filtered = this.workingHours.filter((r) => r.dayOfWeek === dayOfWeek.id);
                const definedEntity = filtered.length === 0 ? null : filtered[0];
                if (definedEntity !== null) {
                    entity.initialize(definedEntity.dayOfWeek, definedEntity.startTime, definedEntity.endTime, true);
                } else {
                    entity.isActive = false;
                }
            }
            this.actualEntities.push(entity);
        }
    }
}
