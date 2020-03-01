import { Component, Input } from '@angular/core';
import { ExpertEntity } from './../../../../entities/index';
@Component({
    selector: 'expert-details-info',
    styleUrls: ['./expertDetailsInfo.scss'],
    templateUrl: './expertDetailsInfo.html',
})
export class ExpertDetailsInfoComponent {
    @Input() entity: ExpertEntity;
}
