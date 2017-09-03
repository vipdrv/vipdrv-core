import { Component } from '@angular/core';

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

    constructor() {
        this.bindedField = 'Binded field';
    }

}
