import { Component, OnInit, OnDestroy, Input, Output, EventEmitter } from '@angular/core';
import { InvitationEntity } from './../../../../entities/index';
@Component({
    selector: 'invitation-details-create',
    styleUrls: ['./invitationDetailsCreate.scss'],
    templateUrl: './invitationDetailsCreate.html',
})
export class InvitationDetailsCreateComponent implements OnInit, OnDestroy {
    @Input() entity: InvitationEntity;
    @Output() onEntityChange: EventEmitter<InvitationEntity> = new EventEmitter<InvitationEntity>();
    /// ctor
    constructor() { }
    /// methods
    ngOnInit(): void { }
    ngOnDestroy(): void { }
    onChangeEmail(newValue: string): void {
        if (this.entity.email !== newValue) {
            this.entity.email = newValue;
            this.notifyOnEntityChange();
        }
    }
    onChangeSiteCount(newValue: number): void {
        if (this.entity.availableSitesCount !== newValue) {
            this.entity.availableSitesCount = newValue;
            this.notifyOnEntityChange();
        }
    }
    protected notifyOnEntityChange() {
        this.onEntityChange.emit(this.entity);
    }
}