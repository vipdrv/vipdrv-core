import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
@Component({
    selector: 'working-hours',
    styleUrls: ['./workingHours.scss'],
    templateUrl: './workingHours.html',
})
export class WorkingHoursComponent implements OnInit {
    @Input() workingHours: string;
    @Output() workingHoursChanged: EventEmitter<string> = new EventEmitter<string>();
    protected actualWorkingHours: string;
    constructor() { }
    ngOnInit(): void {
        this.actualWorkingHours = this.workingHours;
    }
    protected submitWorkingHours(): void {
        this.workingHoursChanged.emit(this.actualWorkingHours);
    }
    protected undoChanges(): void {
        this.actualWorkingHours = this.workingHours;
    }
}