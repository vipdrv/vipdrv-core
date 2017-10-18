import { Component, Input } from '@angular/core';
import { InvitationEntity } from './../../../../entities/index';
@Component({
    selector: 'invitation-details-info',
    styleUrls: ['./invitationDetailsInfo.scss'],
    templateUrl: './invitationDetailsInfo.html',
})
export class InvitationDetailsInfoComponent {
    @Input() entity: InvitationEntity;
}
