import { Component, Input } from '@angular/core';
import { Variable } from './../../../utils/index';
import { SiteEntity } from './../../../entities/index';
@Component({
    selector: 'site-overview',
    styleUrls: ['./siteOverview.scss'],
    templateUrl: './siteOverview.html',
})
export class SiteOverviewComponent {
    @Input() entity: SiteEntity;
    constructor() { }
    protected isEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.entity);
    }
}