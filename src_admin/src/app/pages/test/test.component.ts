import { Component } from '@angular/core';
import { ISiteApiService, SiteApiService } from './../../services/serverApi/index';

@Component({
    selector: 'test',
    styleUrls: ['./test.scss'],
    templateUrl: './test.html',
})
export class Test {

    private _bindedField: string;
    get bindedField(): string {
        return this._bindedField;
    }
    set bindedField(value: string) {
        this._bindedField = value;
    }

    protected siteApiService: ISiteApiService;

    constructor(siteApiService: SiteApiService) {
        this.bindedField = 'Binded field';
        this.siteApiService = siteApiService;
        let defaultSite = this.siteApiService.get(1);
    }

}
