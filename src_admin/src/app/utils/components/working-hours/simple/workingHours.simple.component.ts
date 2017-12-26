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
    /// inputs
    @Input() isComponentReadonly: boolean = false;
    @Input() workingHours: Array<WorkingInterval>;
    /// outputs
    @Output() workingHoursChanged: EventEmitter<Array<WorkingInterval>> = new EventEmitter<Array<WorkingInterval>>();
    /// fields
    private _isSaveProcessing: boolean = false;
    protected actualEntities: Array<DayOfWeekSchedule>;
    protected switcherSettings = ApplicationConstants.switcherSettings;
    /// ctor
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
    protected getStartTimeSelectOptions(interval: DayOfWeekSchedule): Array<any> {
        const options: Array<any> = new Array<any>();
        const endTimeIndex: number = this.timeSelectOptions
            .findIndex(r => r.value === interval.endTime);
        if (endTimeIndex > -1) {
            for (let i = 0; i < endTimeIndex; i++) {
                options.push(this.timeSelectOptions[i]);
            }
        }
        return options;
    }
    protected getEndTimeSelectOptions(interval: DayOfWeekSchedule): Array<any> {
        const options: Array<any> = new Array<any>();
        const startTimeIndex: number = this.timeSelectOptions
            .findIndex(r => r.value === interval.startTime);
        if (startTimeIndex > -1) {
            for (let i = startTimeIndex + 1; i < this.timeSelectOptions.length; i++) {
                options.push(this.timeSelectOptions[i]);
            }
        }
        return options;
    }
    protected changedStartTimeForTimeInterval(interval: DayOfWeekSchedule): void {
        if (interval.isValid()) {
            interval.commitEdit();
            interval.startEdit();
        }
        this.submitWorkingHours();
    }
    protected changedEndTimeForTimeInterval(interval: DayOfWeekSchedule): void {
        if (interval.isValid()) {
            interval.commitEdit();
            interval.startEdit();
        }
        this.submitWorkingHours();
    }
    protected changedIsActiveForTimeInterval(interval: DayOfWeekSchedule): void {
        if (!this.isCheckboxDisabled(interval)) {
            interval.isActive = !interval.isActive;
            this.submitWorkingHours();
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
    protected isSelectStartTimeDisabled(interval: DayOfWeekSchedule): boolean {
        return !interval.isActive || this.isComponentReadonly || this.isSaveProcessing();
    }
    protected isSelectEndTimeDisabled(interval: DayOfWeekSchedule): boolean {
        return !interval.isActive || this.isComponentReadonly || this.isSaveProcessing();
    }
    protected isCheckboxDisabled(interval: DayOfWeekSchedule): boolean {
        return this.isComponentReadonly || this.isSaveProcessing();
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
            entity.isEditProcessing = true;
            this.actualEntities.push(entity);
        }
    }
    /// select options
    protected timeSelectOptions: Array<any> = [
        // {
        //     value: '00:00:00',
        //     displayText: '12:00 AM'
        // },
        // {
        //     value: '00:30:00',
        //     displayText: '12:30 AM'
        // },
        {
            value: '01:00:00',
            displayText: '01:00 AM'
        },
        {
            value: '01:30:00',
            displayText: '01:30 AM'
        },
        {
            value: '02:00:00',
            displayText: '02:00 AM'
        },
        {
            value: '02:30:00',
            displayText: '02:30 AM'
        },
        {
            value: '03:00:00',
            displayText: '03:00 AM'
        },
        {
            value: '03:30:00',
            displayText: '03:30 AM'
        },
        {
            value: '04:00:00',
            displayText: '04:00 AM'
        },
        {
            value: '04:30:00',
            displayText: '04:30 AM'
        },
        {
            value: '05:00:00',
            displayText: '05:00 AM'
        },
        {
            value: '05:30:00',
            displayText: '05:30 AM'
        },
        {
            value: '06:00:00',
            displayText: '06:00 AM'
        },
        {
            value: '06:30:00',
            displayText: '06:30 AM'
        },
        {
            value: '07:00:00',
            displayText: '07:00 AM'
        },
        {
            value: '07:30:00',
            displayText: '07:30 AM'
        },
        {
            value: '08:00:00',
            displayText: '08:00 AM'
        },
        {
            value: '08:30:00',
            displayText: '08:30 AM'
        },
        {
            value: '09:00:00',
            displayText: '09:00 AM'
        },
        {
            value: '09:30:00',
            displayText: '09:30 AM'
        },
        {
            value: '10:00:00',
            displayText: '10:00 AM'
        },
        {
            value: '10:30:00',
            displayText: '10:30 AM'
        },
        {
            value: '11:00:00',
            displayText: '11:00 AM'
        },
        {
            value: '11:30:00',
            displayText: '11:30 AM'
        },
        {
            value: '12:00:00',
            displayText: '12:00 PM'
        },
        {
            value: '12:30:00',
            displayText: '12:30 PM'
        },
        {
            value: '13:00:00',
            displayText: '01:00 PM'
        },
        {
            value: '13:30:00',
            displayText: '01:30 PM'
        },
        {
            value: '14:00:00',
            displayText: '02:00 PM'
        },
        {
            value: '14:30:00',
            displayText: '02:30 PM'
        },
        {
            value: '15:00:00',
            displayText: '03:00 PM'
        },
        {
            value: '15:30:00',
            displayText: '03:30 PM'
        },
        {
            value: '16:00:00',
            displayText: '04:00 PM'
        },
        {
            value: '16:30:00',
            displayText: '04:30 PM'
        },
        {
            value: '17:00:00',
            displayText: '05:00 PM'
        },
        {
            value: '17:30:00',
            displayText: '05:30 PM'
        },
        {
            value: '18:00:00',
            displayText: '06:00 PM'
        },
        {
            value: '18:30:00',
            displayText: '06:30 PM'
        },
        {
            value: '19:00:00',
            displayText: '07:00 PM'
        },
        {
            value: '19:30:00',
            displayText: '07:30 PM'
        },
        {
            value: '20:00:00',
            displayText: '08:00 PM'
        },
        {
            value: '20:30:00',
            displayText: '08:30 PM'
        },
        {
            value: '21:00:00',
            displayText: '09:00 PM'
        },
        {
            value: '21:30:00',
            displayText: '09:30 PM'
        },
        {
            value: '22:00:00',
            displayText: '10:00 PM'
        },
        {
            value: '22:30:00',
            displayText: '10:30 PM'
        },
        {
            value: '23:00:00',
            displayText: '11:00 PM'
        },
        {
            value: '23:30:00',
            displayText: '11:30 PM'
        }



    ];
}
