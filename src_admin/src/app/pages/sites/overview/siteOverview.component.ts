import { Component, Input, OnInit, OnDestroy } from '@angular/core';
import { Variable, ILogger, ConsoleLogger } from '../../../utils/index';
import { SiteEntity } from './../../../entities/index';
import { ApplicationConstants } from './../../../app.constants';
@Component({
    selector: 'site-overview',
    styleUrls: ['./siteOverview.scss'],
    templateUrl: './siteOverview.html',
})
export class SiteOverviewComponent implements OnInit, OnDestroy {
    /// inputs
    @Input() entity: SiteEntity;
    /// properties
    protected switcherSettings = ApplicationConstants.switcherSettings;
    /// injected dependencies
    protected logger: ILogger;
    /// ctor
    constructor(logger: ConsoleLogger) {
        this.logger = logger;
    }
    /// methods
    ngOnInit(): void {
        this.initWidget();
    }
    ngOnDestroy(): void { }
    /// predicates
    protected isEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.entity);
    }
    protected initWidget(): void {
        const TestDrive = (<any>window).TestDrive;

        if (Variable.isNotNullOrUndefined(TestDrive)) {
            TestDrive.init({ SiteId: this.entity.id });
            this.logger.logTrase(`WidgetPreviewComponent: Widget has been initialized for the site (siteId = ${this.entity.id}).`);
        } else {
            this.logger.logTrase(`Widget Failure (siteId = ${this.entity.id}).`);
        }

    }
}