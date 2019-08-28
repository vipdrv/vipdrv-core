import { Component, Input, Output, EventEmitter } from '@angular/core';
import { Extensions, ILogger, ConsoleLogger } from './../../../../utils/index';
import { RouteEntity } from './../../../../entities/index';
import { IRouteValidationService, RouteValidationService } from './../../../../services/index';
import { RoutesConstants } from './../../routes.constants';
@Component({
    selector: 'route-details-edit',
    styleUrls: ['./routeDetailsEdit.scss'],
    templateUrl: './routeDetailsEdit.html',
})
export class RouteDetailsEditComponent {
    /// inputs
    @Input() entity: RouteEntity;
    @Input() isReadOnly: boolean = false;
    @Input() useValidation: boolean = false;
    @Input() forceAcceptImage: boolean = false;
    /// outputs
    @Output() resetForceAcceptImage: EventEmitter<void> = new EventEmitter<void>();
    /// fields
    protected defaultImageUrl: string = RoutesConstants.routeImageDefault;
    protected imageWidth: number = RoutesConstants.routeImageWidth;
    protected imageHeight: number = RoutesConstants.routeImageHeight;
    protected isImageRounded: boolean = RoutesConstants.isRouteImageRounded;
    protected imageAlt: string = RoutesConstants.routeImageAlt;
    protected columnRules: string = 'col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6';
    /// injected dependencies
    protected extensions = Extensions;
    protected logger: ILogger;
    protected validationService: IRouteValidationService;
    /// ctor
    constructor(logger: ConsoleLogger, validationService: RouteValidationService) {
        this.logger = logger;
        this.validationService = validationService;
        this.logger.logDebug('RouteDetailsEditComponent: Component has been constructed.');
    }
    /// methods
    protected onNewImageSelected(newImageUrl: string): void {
        this.entity.photoUrl = newImageUrl;
        this.logger.logTrase('RouteDetailsEditComponent: New route image has been selected.');
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
