import { Component, Input } from '@angular/core';
import { RouteEntity } from './../../../../entities/index';
@Component({
    selector: 'route-details-info',
    styleUrls: ['./routeDetailsInfo.scss'],
    templateUrl: './routeDetailsInfo.html',
})
export class RouteDetailsInfoComponent {
    @Input() entity: RouteEntity;
}
