import { Component, Input } from '@angular/core';
import { LeadEntity } from './../../../../entities/index';
@Component({
    selector: 'lead-details-info',
    styleUrls: ['./leadDetailsInfo.scss'],
    templateUrl: './leadDetailsInfo.html',
})
export class LeadDetailsInfoComponent {
    @Input() entity: LeadEntity;
}
