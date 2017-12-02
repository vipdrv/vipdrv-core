import { Component, Input } from '@angular/core';
import { ILogger, ConsoleLogger } from './../../../../utils/index';
import { SiteEntity } from './../../../../entities/index';
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
    /// fields
    defaultImageUrl: string = SitesConstants.SiteImageDefault;
    imageWidth: number = 300;
    imageHeight: number = 200;
    isImageRounded: boolean = false;
    imageAlt: string = 'site image';
    columnRules: string = 'col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6';
    /// injected dependencies
    protected logger: ILogger;
    /// ctor
    constructor(logger: ConsoleLogger) {
        this.logger = logger;
        this.logger.logDebug('SiteDetailsEditComponent: Component has been constructed.');
    }
    /// methods
    onNewSiteImageSelected(newImageUrl: string): void {
        this.entity.imageUrl = newImageUrl;
        this.logger.logTrase('SiteDetailsEditComponent: New site image has been selected.');
    }
    /// predicates
    isImageComponentReadOnly(): boolean {
        return this.isReadOnly;
    }
    /// methods for properties
    // site name
    protected getSiteNameInputClass(): any {
        return {
            'test-drive-invalid-input': !this.isSiteNameValid()
        };
    }
    protected isSiteNameInputDisabled(): boolean {
        return this.isReadOnly;
    }
    protected isSiteNameValid(): boolean {
        return false;
    }
    protected getSiteNameInvalidMessageKey(): string {
        return 'sites.modal.siteNameValidationMessage';
    }
    // site url
    protected getSiteUrlInputClass(): any {
        return {
            'test-drive-invalid-input': !this.isSiteUrlValid()
        };
    }
    protected isSiteUrlInputDisabled(): boolean {
        return this.isReadOnly;
    }
    protected isSiteUrlValid(): boolean {
        return false;
    }
    protected getSiteUrlInvalidMessageKey(): string {
        return 'sites.modal.siteNameValidationMessage';
    }
}
