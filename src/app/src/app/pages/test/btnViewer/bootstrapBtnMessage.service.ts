import {Injectable} from '@angular/core';

@Injectable()
export class BootstrapBtnMessageService {

    constructor() {}

    getWrappedMessage(btnType: string): string{
        return "Btstrp btn " + btnType;
    }
}