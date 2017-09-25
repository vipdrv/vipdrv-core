import { Component, Input, Output, EventEmitter, OnInit, OnChanges, SimpleChanges, SimpleChange } from '@angular/core';
import { Variable, Extensions } from './../../index';
import { daysOfWeek } from './../../../constants/index';
import { WorkingInterval } from './workingInterval';
@Component({
    selector: 'working-hours',
    styleUrls: ['./workingHours.scss'],
    templateUrl: './workingHours.html',
})
export class WorkingHoursComponent implements OnInit, OnChanges {
    protected daysOfWeek = daysOfWeek;
    @Input() workingHours: Array<WorkingInterval>;
    @Output() workingHoursChanged: EventEmitter<Array<WorkingInterval>> = new EventEmitter<Array<WorkingInterval>>();
    protected actualEntities: Array<WorkingInterval>;
    protected newEntity: WorkingInterval;
    constructor() { }
    ngOnInit(): void {
        this.actualEntities = this.workingHours;
        this.initializeNewEntity();
    }
    ngOnChanges(changes: SimpleChanges) {
        let workingHoursChange: SimpleChange = changes['workingHours'];
        if (Variable.isNotNullOrUndefined(workingHoursChange) &&
            this.actualEntities !== this.workingHours) {
            this.actualEntities = this.workingHours;
        }
    }
    protected submitWorkingHours(): void {
        this.workingHoursChanged.emit(this.actualEntities);
    }
    protected undoChanges(): void {
        this.actualEntities = this.workingHours;
    }
    protected addNewInterval(): void {
        this.newEntity.commitEdit();
        this.actualEntities.push(this.newEntity);
        this.initializeNewEntity();
        this.submitWorkingHours();
    }
    protected deleteInterval(entity: any): void {
        let index = this.actualEntities.findIndex((r) => r === entity);
        if (index > -1) {
            this.actualEntities.splice(index, 1);
        }
        this.submitWorkingHours();
    }
    /// predicates
    protected isIntervalValid(entity: WorkingInterval, checkEdit: boolean = false): boolean {
        return entity.dayOfWeek >= 0 && entity.dayOfWeek <= 6 &&
            Extensions.regExp.time.test(entity.startTime) &&
            Extensions.regExp.time.test(entity.endTime) &&
            (
                checkEdit ||
                entity.editDayOfWeek >= 0 && entity.editDayOfWeek <= 6 &&
                Extensions.regExp.time.test(entity.editStartTime) &&
                Extensions.regExp.time.test(entity.editEndTime)
            );
    }
    /// helpers
    private initializeNewEntity() {
        this.newEntity = new WorkingInterval();
        this.newEntity.initialize(0, '09:00:00', '18:00:00');
    }
}