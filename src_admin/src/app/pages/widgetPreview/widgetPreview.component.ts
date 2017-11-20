import { Component, Input, OnInit } from '@angular/core';
import { Variable, ConsoleLogger, ILogger } from './../../utils/index';
import {logger} from "codelyzer/util/logger";

@Component({
    selector: 'widget-preview',
    styleUrls: ['./widgetPreview.scss'],
    templateUrl: './widgetPreview.html',
})
export class WidgetPreviewComponent implements OnInit {
    @Input() id: number;

    /// injected dependencies
    protected logger: ILogger;

    /// ctor
    constructor(logger: ConsoleLogger) {
        this.logger = logger;
        this.logger.logDebug('WidgetPreviewComponent has been constructed.');
    }

    ngOnInit() {
        this.initWidget();
    }

    initWidget(): void {
        (<any>window).TestDrive.init({ SiteId: this.id });
        this.logger.logDebug(`Widget initialized for site: ${this.id}`);
    }
}
