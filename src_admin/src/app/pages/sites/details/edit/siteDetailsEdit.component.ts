import { Component, Input, Output, EventEmitter } from '@angular/core';
import { Variable, Extensions, ILogger, ConsoleLogger, WorkingInterval } from './../../../../utils/index';
import { SiteEntity } from './../../../../entities/index';
import { ISiteValidationService, SiteValidationService } from './../../../../services/index';
import { ISiteEntityPolicyService, SiteEntityPolicyService } from './../../../../services/index';
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
    @Input() isWeekScheduleOpenedByDefault: boolean = false;
    @Input() ownerOptions: Array<any>;
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
    protected entityPolicyService: ISiteEntityPolicyService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        siteValidationService: SiteValidationService,
        entityPolicyService: SiteEntityPolicyService) {
        this.logger = logger;
        this.siteValidationService = siteValidationService;
        this.entityPolicyService = entityPolicyService;
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
    protected getOwnerOptions(): Array<any> {
        return this.ownerOptions.filter(r => Variable.isNotNullOrUndefined(r.value));
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
    protected isSiteOwnerInputDisabled(): boolean {
        return this.isReadOnly;
    }
    protected isSiteOwnerValid(): boolean {
        return this.siteValidationService.isOwnerValid(this.entity);
    }
    protected getSiteOwnerInvalidMessageKey(): string {
        return this.siteValidationService.getInvalidOwnerMessageKey(this.entity);
    }
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
    protected getWeekScheduleDefaultClass() {
        return this.isWeekScheduleOpenedByDefault ? 'show' : '';
    }
    // WASP
    protected isSiteWASPUrlInputDisabled(): boolean {
        return this.isReadOnly;
    }
    protected isSiteWASPUrlValid(): boolean {
        return this.siteValidationService.isWASPUrlValid(this.entity);
    }
    protected getSiteWASPUrlInvalidMessageKey(): string {
        return this.siteValidationService.getInvalidWASPUrlMessageKey(this.entity);
    }
    // zip code
    protected isZipCodeInputDisabled(): boolean {
        return this.isReadOnly;
    }
    protected isZipCodeValid(): boolean {
        return this.siteValidationService.isZipCodeValid(this.entity);
    }
    protected getZipCodeInvalidMessageKey(): string {
        return this.siteValidationService.getInvalidZipCodeMessageKey(this.entity);
    }
    // zip code
    protected isAvailableTestDriveFromHomeInputDisabled(): boolean {
        return this.isReadOnly;
    }
    protected isAvailableTestDriveFromHomeValid(): boolean {
        return this.siteValidationService.isAvailableTestDriveFromHomeValid(this.entity);
    }
    protected getAvailableTestDriveFromHomeInvalidMessageKey(): string {
        return this.siteValidationService.getInvalidAvailableTestDriveFromHomeMessageKey(this.entity);
    }
    // max delivery distance
    protected isMaxDeliveryDistanceInputDisabled(): boolean {
        return this.isReadOnly;
    }
    protected isMaxDeliveryDistanceValid(): boolean {
        return this.siteValidationService.isMaxDeliveryDistanceValid(this.entity);
    }
    protected getMaxDeliveryDistanceInvalidMessageKey(): string {
        return this.siteValidationService.getInvalidMaxDeliveryDistanceMessageKey(this.entity);
    }
}
