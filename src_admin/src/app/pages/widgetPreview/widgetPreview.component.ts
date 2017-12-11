import { Component, Input, OnInit } from '@angular/core';
import { Variable, ConsoleLogger, ILogger } from './../../utils/index';
@Component({
    selector: 'widget-preview',
    styleUrls: ['./widgetPreview.scss'],
    templateUrl: './widgetPreview.html',
})
export class WidgetPreviewComponent implements OnInit {
    /// inputs
    @Input() id: number;
    /// injected dependencies
    protected logger: ILogger;
    /// ctor
    constructor(logger: ConsoleLogger) {
        this.logger = logger;
        this.logger.logDebug('WidgetPreviewComponent: Component has been constructed.');
    }
    /// methods
    ngOnInit() {
        this.initWidget();
    }
    protected initWidget(): void {
        (<any>window).TestDrive.init({ SiteId: this.id });
        this.logger.logTrase(`WidgetPreviewComponent: Widget has been initialized for the site (siteId = ${this.id}).`);
    }
}
