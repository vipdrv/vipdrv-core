import { Component, Input } from '@angular/core';
import { BeverageEntity } from './../../../../entities/index';
@Component({
    selector: 'beverage-details-info',
    styleUrls: ['./beverageDetailsInfo.scss'],
    templateUrl: './beverageDetailsInfo.html',
})
export class BeverageDetailsInfoComponent {
    @Input() entity: BeverageEntity;
}
