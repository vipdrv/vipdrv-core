import { Component, Input } from '@angular/core';
import { Extensions, ILogger, ConsoleLogger, WorkingInterval } from './../../../../utils/index';
import { ExpertEntity } from './../../../../entities/index';
import { IExpertValidationService, ExpertValidationService } from './../../../../services/index';
import { ExpertsConstants } from './../../experts.constants';
@Component({
    selector: 'expert-details-edit',
    styleUrls: ['./expertDetailsEdit.scss'],
    templateUrl: './expertDetailsEdit.html',
})
export class ExpertDetailsEditComponent {
    /// inputs
    @Input() entity: ExpertEntity;
    @Input() isReadOnly: boolean = false;
    @Input() useValidation: boolean = false;
    /// fields
    protected defaultImageUrl: string = ExpertsConstants.expertImageDefault;
    protected imageWidth: number = ExpertsConstants.expertImageWidth;
    protected imageHeight: number = ExpertsConstants.expertImageHeight;
    protected isImageRounded: boolean = ExpertsConstants.isExpertImageRounded;
    protected imageAlt: string = ExpertsConstants.expertImageAlt;
    protected columnRules: string = 'col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6';
    /// injected dependencies
    protected extensions = Extensions;
    protected logger: ILogger;
    protected validationService: IExpertValidationService;
    /// ctor
    constructor(logger: ConsoleLogger, validationService: ExpertValidationService) {
        this.logger = logger;
        this.validationService = validationService;
        this.logger.logDebug('ExpertDetailsEditComponent: Component has been constructed.');
    }
    /// methods
    protected onNewImageSelected(newImageUrl: string): void {
        this.entity.photoUrl = newImageUrl;
        this.logger.logTrase('ExpertDetailsEditComponent: New expert image has been selected.');
    }
    protected onActualizeWorkingHours(newWorkingHours: Array<WorkingInterval>): void {
        this.entity.workingHours = newWorkingHours;
    }
    /// predicates
    protected isImageComponentReadOnly(): boolean {
        return this.isComponentReadOnly();
    }
    protected isWeekScheduleComponentReadOnly(): boolean {
        return this.isComponentReadOnly();
    }
    protected isValidationActive(): boolean {
        return this.useValidation;
    }
    protected isComponentReadOnly(): boolean {
        return this.isReadOnly;
    }
}
