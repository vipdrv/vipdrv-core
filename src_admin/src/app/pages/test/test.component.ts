import { Component } from '@angular/core';
import { SiteApiService } from "./../../services/serverApi/site/site.api-service";

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
    constructor(public siteApiService: SiteApiService) {
        this.bindedField = "Binded field";
        let a = this.siteApiService.get(1);
    }

}
