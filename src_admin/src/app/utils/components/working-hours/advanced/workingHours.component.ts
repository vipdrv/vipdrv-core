import { Component, Input, Output, EventEmitter, OnInit, OnChanges, SimpleChanges, SimpleChange } from '@angular/core';
import { Variable } from '../../../index';
import { daysOfWeek } from '../../../../constants/index';
import { WorkingInterval } from '../models/workingInterval';
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
        this.undoChanges();
        this.initializeNewEntity();
    }
    ngOnChanges(changes: SimpleChanges) {
        const workingHoursChange: SimpleChange = changes['workingHours'];
        if (Variable.isNotNullOrUndefined(workingHoursChange) &&
            this.actualEntities !== this.workingHours) {
            this.undoChanges();
        }
    }
    protected submitWorkingHours(): void {
        this.workingHoursChanged.emit(this.actualEntities);
    }
    protected undoChanges(): void {
        if (Variable.isNotNullOrUndefined(this.workingHours)) {
            this.actualEntities = this.workingHours;
        } else {
            this.actualEntities = [];
        }
    }
    protected addNewInterval(): void {
        this.newEntity.commitEdit();
        this.actualEntities.push(this.newEntity);
        this.initializeNewEntity();
        this.submitWorkingHours();
    }
    protected deleteInterval(entity: any): void {
        const index = this.actualEntities.findIndex((r) => r === entity);
        if (index > -1) {
            this.actualEntities.splice(index, 1);
        }
        this.submitWorkingHours();
    }
    /// helpers
    private initializeNewEntity() {
        this.newEntity = new WorkingInterval();
        this.newEntity.initialize(0, '09:00:00', '18:00:00');
    }
}