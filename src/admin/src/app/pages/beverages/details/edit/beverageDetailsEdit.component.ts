import { Component, Input, Output, EventEmitter } from '@angular/core';
import { Extensions, ILogger, ConsoleLogger } from './../../../../utils/index';
import { BeverageEntity } from './../../../../entities/index';
import { IBeverageValidationService, BeverageValidationService } from './../../../../services/index';
import { BeveragesConstants } from './../../beverages.constants';
@Component({
    selector: 'beverage-details-edit',
    styleUrls: ['./beverageDetailsEdit.scss'],
    templateUrl: './beverageDetailsEdit.html',
})
export class BeverageDetailsEditComponent {
    /// inputs
    @Input() entity: BeverageEntity;
    @Input() isReadOnly: boolean = false;
    @Input() useValidation: boolean = false;
    @Input() forceAcceptImage: boolean = false;
    /// outputs
    @Output() resetForceAcceptImage: EventEmitter<void> = new EventEmitter<void>();
    /// fields
    protected defaultImageUrl: string = BeveragesConstants.beverageImageDefault;
    protected imageWidth: number = BeveragesConstants.beverageImageWidth;
    protected imageHeight: number = BeveragesConstants.beverageImageHeight;
    protected isImageRounded: boolean = BeveragesConstants.isBeverageImageRounded;
    protected imageAlt: string = BeveragesConstants.beverageImageAlt;
    protected columnRules: string = 'col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6';
    /// injected dependencies
    protected extensions = Extensions;
    protected logger: ILogger;
    protected validationService: IBeverageValidationService;
    /// ctor
    constructor(logger: ConsoleLogger, validationService: BeverageValidationService) {
        this.logger = logger;
        this.validationService = validationService;
        this.logger.logDebug('BeverageDetailsEditComponent: Component has been constructed.');
    }
    /// methods
    protected onNewImageSelected(newImageUrl: string): void {
        this.entity.photoUrl = newImageUrl;
        this.logger.logTrase('BeverageDetailsEditComponent: New beverage image has been selected.');
    }
    protected onResetForceAcceptImage(): void {
        this.resetForceAcceptImage.emit();
    }
    /// predicates
    protected isImageComponentReadOnly(): boolean {
        return this.isComponentReadOnly();
    }
    protected isValidationActive(): boolean {
        return this.useValidation;
    }
    protected isComponentReadOnly(): boolean {
        return this.isReadOnly;
    }
}
