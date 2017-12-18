import { Component, Input, Output, EventEmitter } from '@angular/core';
import { Extensions, ILogger, ConsoleLogger, WorkingInterval } from './../../../../utils/index';
import { SiteEntity } from './../../../../entities/index';
import { ISiteValidationService, SiteValidationService } from './../../../../services/index';
import { SitesConstants } from './../../sites.constants';
@Component({
    selector: 'site-details-edit',
    styleUrls: ['./siteDetailsEdit.scss'],
    templateUrl: './siteDetailsEdit.html',
})
export class SiteDetailsEditComponent {
    /// inputs
    @Input() entity: SiteEntity;
    @Input() isReadOnly: boolean = false;
    @Input() useValidation: boolean = false;
    @Input() forceAcceptImage: boolean = false;
    /// outputs
    @Output() resetForceAcceptImage: EventEmitter<void> = new EventEmitter<void>();
    /// fields
    protected defaultImageUrl: string = SitesConstants.siteImageDefault;
    protected imageWidth: number = SitesConstants.siteImageWidth;
    protected imageHeight: number = SitesConstants.siteImageHeight;
    protected isImageRounded: boolean = SitesConstants.isSiteImageRounded;
    protected imageAlt: string = SitesConstants.siteImageAlt;
    protected columnRules: string = 'col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6';
    /// injected dependencies
    protected extensions = Extensions;
    protected logger: ILogger;
    protected siteValidationService: ISiteValidationService;
    /// ctor
    constructor(logger: ConsoleLogger, siteValidationService: SiteValidationService) {
        this.logger = logger;
        this.siteValidationService = siteValidationService;
        this.logger.logDebug('SiteDetailsEditComponent: Component has been constructed.');
    }
    /// methods
    protected onNewSiteImageSelected(newImageUrl: string): void {
        this.entity.imageUrl = newImageUrl;
        this.logger.logTrase('SiteDetailsEditComponent: New site image has been selected.');
    }
    protected onResetForceAcceptImage(): void {
        this.resetForceAcceptImage.emit();
    }
    protected onActualizeWorkingHours(newWorkingHours: Array<WorkingInterval>): void {
        this.entity.workingHours = newWorkingHours;
    }
    /// predicates
    protected isImageComponentReadOnly(): boolean {
        return this.isReadOnly;
    }
    protected isValidationActive(): boolean {
        return this.useValidation;
    }
    protected isWeekScheduleComponentReadOnly(): boolean {
        return this.isReadOnly;
    }
    /// methods for properties
    // site name
    protected isSiteNameInputDisabled(): boolean {
        return this.isReadOnly;
    }
    protected isSiteNameValid(): boolean {
        return this.siteValidationService.isNameValid(this.entity);
    }
    protected getSiteNameInvalidMessageKey(): string {
        return this.siteValidationService.getInvalidNameMessageKey(this.entity);
    }
    // site url
    protected isSiteUrlInputDisabled(): boolean {
        return this.isReadOnly;
    }
    protected isSiteUrlValid(): boolean {
        return this.siteValidationService.isUrlValid(this.entity);
    }
    protected getSiteUrlInvalidMessageKey(): string {
        return this.siteValidationService.getInvalidUrlMessageKey(this.entity);
    }
}
