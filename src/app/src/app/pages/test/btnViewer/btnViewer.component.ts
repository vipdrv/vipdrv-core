import { Component } from '@angular/core';

import { BootstrapBtnMessageService } from './bootstrapBtnMessage.service';

@Component({
    selector: 'btn-viewer',
    templateUrl: './btnViewer.html',
    styleUrls: ['./btnViewer.scss']
})
export class BtnViewer {

    protected btnMsgService: BootstrapBtnMessageService;

    constructor(btnMsgService: BootstrapBtnMessageService) {
        this.btnMsgService = btnMsgService;
    }

    protected getBtnText(btnType: string): string {
        return this.btnMsgService.getWrappedMessage(btnType);
    }
}