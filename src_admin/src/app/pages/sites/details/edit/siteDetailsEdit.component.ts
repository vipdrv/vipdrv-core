import { Component, Input } from '@angular/core';
import { SiteEntity } from './../../../../entities/index';
@Component({
    selector: 'site-details-edit',
    styleUrls: ['./siteDetailsEdit.scss'],
    templateUrl: './siteDetailsEdit.html',
})
export class SiteDetailsEditComponent {
    @Input() entity: SiteEntity;
}
